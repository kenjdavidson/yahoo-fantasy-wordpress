<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.  Logging
 * into Yahoo! OAuth is a two.5 step process:
 * 
 * 1) The first step is registering for a Yahoo! developer account.  This will
 * provided the user with a Consumer Key and a Consumer Secret.  These must 
 * be entered into the Step 1 fields.
 * 
 * 2) The user must then refresh the page, doing so will create the required
 * request token and request secret, as well as provide the user with the URL
 * used to login.  Once the user logs in and confirms access, they should copy
 * the provided request verifier into the field, and then save changes to
 * continue.
 * 
 * 3) With the verifier, the final step will request an access token and session
 * token.  Once this has been completed they should then be connected and will
 * be allowed to make requests for ~1h.  After the 1h, they will need the 
 * session token to refresh the connection, this is usually done automatically.
 *
 * @link       http://php.net/manual/en/book.oauth.php
 * @link       https://github.com/joechung/oauth_yahoo
 * @since      1.0.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/admin/partials
 */

// Consumer Key and Secret
$consumer_key = trim(get_option('yf_consumer_key'));
$consumer_secret = trim(get_option('yf_consumer_secret'));

if (!$consumer_key) {
    $consumer_key = "Enter Consumer Key";
}

if (!$consumer_secret) {
    $consumer_secret = "Enter Consumer Secret";
}

// Access Key, Secret and Session if available
$access_token = trim(get_option('yf_access_token'));
$access_secret = trim(get_option('yf_access_secret'));
$access_session = trim(get_option('yf_access_session'));

// Request Verifier, Token and Secret if available.
$request_verifier = trim(get_option('yf_request_verifier'));
$request_token = trim(get_option('yf_request_token'));
$request_secret = trim(get_option('yf_request_secret'));

// Create a new Yahoo_OAuth Object.  This Object is used to create and maintain
// the OAuth user connection.
$o = new Yahoo_OAuth($consumer_key, 
        $consumer_secret, 
        $access_secret, 
        $access_token, 
        $access_session,
        $request_verifier);
$o->setDebug(true);    

// Initialize as false
$auth_success = false;

?>
<div>
    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
    <form method="post" action="options.php">
        <?php do_settings_sections('yfoption-group'); ?>
        <?php settings_fields('yfoption-group'); ?>
        
        <div class="wrap">
            <?php
            
            // Get the Users game list request URL, used for testing the
            // connection to Yahoo! services.  Just because it's the fastest
            // request
            $url = Yahoo_Sports_API::USER_API_URL;
            
            // If we have an access token and a request verifier available, 
            // we should be able to make the request successfully.  If so
            // set auth_success to true.  If the request fails, then attempt
            // a single refresh, if that is successful make sure we save the 
            // new values.
            if ($access_token && $request_verifier) {
                yahooSportsLogger('Attempting Admin request: User resource');
                $auth_success = $o->request($url); 
               
                // If the request was unsuccessful and the error code was
                // 401, then we can attempt to refresh the connection
                if (!$auth_success) {  
                    yahooSportsLogger('Attempting Admin token refresh');

                    // If this is successful we need to make sure we update
                    // the values and make sure the user knows to save them.
                    if ($o->refreshAccessToken()) {                                                
                        $access_secret = $o->getAccessSecret();
                        $access_token = $o->getAccessToken();
                        $access_session = $o->getAccessSession();
                        $auth_success = true;

                        yahooSportsLogger('Successful Admin token refresh: secret='); 

                        update_option('yf_access_token', $access_token);
                        update_option('yf_access_secret', $access_secret);
                        update_option('yf_access_session', $access_session);
                    }
                }                 
            }
            
            // Set the status text based on the final result of connection
            // or refresh of token.
            $status = $auth_success ? 'Connected' : 'Not connected';
            yahooSportsLogger('Connection status: ' . $status);
            ?>
            
            <h1>Connection Status: <?php echo $status; ?></h1>
            <?php
                if ($auth_success) {
                    echo 'The plugin should be working as expected.';
                } else {
                    echo 'There was a problem refreshing or connecting to the Yahoo! services.  Please continue '
                        . 'the login process, or reset and start again.  The latest response from the Yahoo! '
                        . 'services was: ' . $o->getOauthMessage();
                }
            ?>
            <h1>Step 1: Yahoo! API Keys</h1>
            <p>
                In order to setup the Yahoo! Fantasy Sports plugin, you must first register
                with Yahoo! to get developer keys.  The Consumer and Private keys are used
                to connect with the Yahoo! services security.  If you don't already have a key, 
                click <a href="https://developer.yahoo.com/fantasysports/guide/">here</a> and follow
                the instructions. 
            </p>
            <p>
                Once you have provided the consumer and secret keys, click <b>Save Changes</b> to
                move on the the verification step.
            </p>

            <div class="option-field">
                <span>Yahoo Fantasy Consumer Key:</span>
                <input type="text" 
                       name="yf_consumer_key" 
                       value="<?php echo esc_attr($consumer_key); ?>" />
            </div>

            <div class="option-field">
                <span>Yahoo Fantasy Consumer Secret:</span>
                <input type="text" 
                       name="yf_consumer_secret" 
                       value="<?php echo esc_attr($consumer_secret); ?>" />
            </div>  
            
            <h1>Step 2: Yahoo! Request Tokens</h1>

            <?php
            if (!$auth_success && !$request_verifier) {
                yahooSportsLogger('Admin getting request URL for validation');
                
                /*
                 * Clear out the access fields so that they can be reset.
                 */
                $access_token = null;
                $access_secret = null;
                $access_session = null;
                
                /*
                 * There was no $access_token ever saved. This means its the first time
                 * the user has opened the Admin page.  We must provide them with a link
                 * to the Verification page and allow them to enter the verification value
                 * when they receive it.
                 */
                
                try {
                    $requestUrl = $o->getVerifierUrl();
                    $request_token = $o->getRequestToken();
                    $request_secret = $o->getRequestSecret();                    
                } catch (OAuthException $ex) {
                    // Ignore as we'll display empty values
                }   
                
                ?>
                <p>
                    It looks like you've never attempted to sign in to Yahoo! OAuth before.  To continue, you 
                    need to open this <a href="<?php echo $requestUrl; ?>" target="_blank">link</a>.
                    Once you login to Yahoo! and accept the security settings, you will need to copy the 6 digit
                    number into the OAuth Verifier input and click <b>Save Changes</b> to continue.
                </p>              
                <?php
            
            } else if (!$auth_success && $request_verifier) {
                ?>
                <p>
                    You have provided the access verifier and a new access token has been created, confirm
                    the three access tokens in Step 3 and click Save Changes to finalize the connection.
                </p>                
                <?php
            } else {
                ?>
                <p>
                    You've logged in and provided access.  Step three will attempt to login
                    and confirm that you're authentication was accepted.
                </p>
                <?php
            }         
            ?>
                    
            <div class="option-field">
                <span>Request Verifier:</span>
                <input type="text" 
                       class="access-input"
                       name="yf_request_verifier" 
                       value="<?php echo esc_attr($request_verifier); ?>" 
                       enabled="false" />
            </div>   
            
            <div class="option-field">
                <span>Request Token:</span>
                <input type="text" 
                       class="access-input"
                       name="yf_request_token" 
                       value="<?php echo esc_attr($request_token); ?>" />
            </div> 
            
            <div class="option-field">
                <span>Request Secret:</span>
                <input type="text" 
                       class="access-input"
                       name="yf_request_secret" 
                       value="<?php echo esc_attr($request_secret); ?>" />
            </div>              

            <h1>Step 3: Yahoo! Access Token</h1>
            <p>
                The last step uses the provided verifier to connect request the
                access token.  The access token and session, are what will be used
                moving forward to refresh and open any connection to the Yahoo!
                services.                       
            </p>     
            
            <?php           
            if (!$auth_success && !$access_token && $request_verifier) {
                yahooSportsLogger('Admin getting Access token with ' . $request_verifier);
                
                /*
                 * Now that we have the $request_verifier and the request tokens
                 * we can login and get the Access token.  This is the last part
                 * of the login.
                 */
                try {

                    $o->requestAccessToken($request_token, $request_secret);       // HTTP GET required

                    $access_token = $o->getAccessToken();
                    $access_secret = $o->getAccessSecret();
                    $access_session = $o->getAccessSession();

                    $auth_success = true;
                    $auth_message = "Successfully obtained access token!  "
                            . "Click <b>Save Cbanges</b> to complete the process.";
                                        
                } catch (Exception $ex) {

                    $auth_success = false;
                    $auth_message = $ex->getMessage();
                    echo 'OAuth failure: ' . $auth_message . '  You\'ll have to '
                            . 'reset and try agian.';                    
                }
            }            
            ?>

            <div class="option-field">
                <span>OAuth Access Token:</span>
                <input type="text" 
                       class="access-input"
                       name="yf_access_token" 
                       value="<?php echo esc_attr($access_token); ?>" />
            </div> 
            
            <div class="option-field">
                <span>OAuth Access Secret:</span>
                <input type="text" 
                       class="access-input"
                       name="yf_access_secret" 
                       value="<?php echo esc_attr($access_secret); ?>" />
            </div>                                 
            
            <div class="option-field">
                <span>OAuth Access Session:</span>
                <input type="text" 
                       class="access-input"
                       name="yf_access_session" 
                       value="<?php echo esc_attr($access_session); ?>" />
            </div>                            

        </div>

        <?php submit_button(); ?>
        
        <input type="button"
               id="btnReset"
               value="Reset Login" />
        
    </form>
</div>

