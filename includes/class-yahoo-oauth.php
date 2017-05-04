<?php

/**
 * Yahoo_OAuth handles all the creation and connection requests to to the
 * Yahoo! OAuth services.
 *
 * @since 2.0.0
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includes
 * @author     Ken Davidson <ken.j.davidson@live.ca>
 */

require 'oauth/oauth_global.php';
require 'oauth/oauth_helper.php';

class Yahoo_OAuth {

    /**
     * Consumer API Key
     * @var String 
     */
    protected $consumerKey;

    /**
     * Consumer API Secret
     * @var String
     */
    protected $consumerSecret;

    /**
     * Access Secret used to connect via OAuth
     * @var String
     */
    protected $accessSecret;

    /**
     * Access Session used to connect via OAuth
     * @var String
     */
    protected $accessSession;

    /**
     * Access Token used to connect via OAuth
     * @var String
     */
    protected $accessToken;

    /**
     * Request Token used to request access via OAuth
     * @var String
     */
    protected $requestToken;

    /**
     * Request Secret used to request access via OAuth
     * @var String
     */
    protected $requestSecret;

    /**
     * Request Verifier provided by request service and used to create
     * Access Token
     * @var String
     */
    protected $requestVerifier;

    /**
     * OAuth
     * @var OAuth
     */
    protected $oauth;

    /**
     * Debug flag
     * @var bool
     */
    private $debug = false;
    
    /**
     * Last message response from the server
     * @var type 
     */
    private $oauthMessage;

    /**
     * Creates a new Yahoo_OAuth object.
     * 
     * @param String $consumerKey
     * @param String $consumerSecret
     * @param String $accessSecret
     * @param String $accessSession
     * @param String $accessToken
     * @param String $callable
     */
    public function __construct($consumerKey, 
            $consumerSecret, 
            $accessSecret = null, 
            $accessToken = null, 
            $accessSession = null, 
            $requestVerifier = null,
            $callback = null) {

        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->accessSecret = $accessSecret;
        $this->accessToken = $accessToken;
        $this->accessSession = $accessSession;
        $this->requestVerifier = $requestVerifier;
        $this->refreshCallback = $callback;

        $this->oauth = new OAuth($this->consumerKey, 
                $this->consumerSecret, 
                OAUTH_SIG_METHOD_HMACSHA1, 
                OAUTH_AUTH_TYPE_URI);
        $this->oauth->setToken($this->accessToken, $this->accessSecret);
    }

    /**
     * Enable or disable OAuth debugging.  Debugging turns on the 
     * @param bool $d
     */
    public function setDebug($d) {
        $this->debug = $d;
       
        if ($d) {
            $this->oauth->enableDebug();
        } else {
            $this->oauth->disableDebug();
        }
    }

    /**
     * Attempts to make an OAuth request to the specified $endpoint.  This method
     * makes a single refresh, and returns either True or False, based on 
     * whether there was a successful response.
     * 
     * @param String $endpoint
     * @param String $method
     * @return 
     * @throws Exception
     */
    public function request($endpoint, $method = 'GET') {  
        
        $resOk = false;
        
        try {                      
            $this->logDebug('Yahoo_OAuth fetch: ' . $endpoint, true);  
            $resOk = $this->oauth->fetch($endpoint, null, $method);             
        } catch (Exception $fetchEx) {
            $this->logDebug('Yahoo_OAuth request exception: ' . $fetchEx->getMessage());
        }
        
        $this->logDebug('Yahoo_OAuth fetch completed: ' . $endpoint);
        return $resOk;
    }

    /**
     * Refresh an access token using an expired request token.  This refresh is
     * done manually, and taken from https://github.com/joechung/oauth_yahoo/blob/master/refacctok.php
     * as a backup and test to the PECL OAuth module.
     * 
     * @param string $consumer_key obtained when you registered your app
     * @param string $consumer_secret obtained when you registered your app
     * @param string $old_access_token obtained previously
     * @param string $old_token_secret obtained previously
     * @param string $oauth_session_handle obtained previously
     * @param bool $usePost use HTTP POST instead of GET (default false)
     * @param bool $useHmacSha1Sig use HMAC-SHA1 signature (default false)
     * @return response string with token or empty array on error
     */
    function refreshAccessToken($useHmacSha1Sig = true, 
            $passOAuthInHeader = true, 
            $usePost = false) {
        
        $resOk = false;
        $response = array();
        $url = 'https://api.login.yahoo.com/oauth/v2/get_token';
        $params['oauth_version'] = '1.0';
        $params['oauth_nonce'] = mt_rand();
        $params['oauth_timestamp'] = time();
        $params['oauth_consumer_key'] = $this->consumerKey;
        $params['oauth_token'] = $this->accessToken;
        $params['oauth_session_handle'] = $this->accessSession;
        
        // Compute signature and add it to the params list.  Signature is
        // created by using the consumerSecret and accessSecret
        if ($useHmacSha1Sig) {
            $params['oauth_signature_method'] = 'HMAC-SHA1';
            $params['oauth_signature'] = oauth_compute_hmac_sig(
                    $usePost ? 'POST' : 'GET', 
                    $url, 
                    $params, 
                    $this->consumerSecret, 
                    $this->accessSecret);
        } 
        
        // Pass OAuth credentials in a separate header or in the query string
        if ($passOAuthInHeader) {
            $query_parameter_string = oauth_http_build_query($params, true);
            $header = build_oauth_header($params, "yahooapis.com");
            $headers[] = $header;
        } else {
            $query_parameter_string = oauth_http_build_query($params);
        }
        
        // POST or GET the request
        if ($usePost) {
            $request_url = $url;
            $this->logDebug("Yahoo_OAuth: Refresh access token post url:" . $request_url);
            $this->logDebug("Yahoo_OAuth: Refresh access token post body:" . $query_parameter_string);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $response = do_post($request_url, $query_parameter_string, 443, $headers);
        } else {
            $request_url = $url . ($query_parameter_string ? ('?' . $query_parameter_string) : '' );
            $this->logDebug("Yahoo_OAuth: Refresh access token get url:" . $request_url, true);
            $response = do_get($request_url, 443, $headers);
        }              
        
        // Extract successful response
        if (!empty($response)) {           
            list($info, $header, $body) = $response;
            $body_parsed = oauth_parse_str($body); 
            
            // Display the body parsed content to the screen.  Only used for
            // debugging and confirmation purposes.
            //print_r($body_parsed);
            
            if (!array_key_exists('oauth_problem', $body_parsed)) {
                $this->setAccessTokens(rfc3986_decode($body_parsed['oauth_token']), 
                        rfc3986_decode($body_parsed['oauth_token_secret']), 
                        rfc3986_decode($body_parsed['oauth_session_handle']));            
                $this->logDebug('Yahoo_Oauth: Refresh Access Token successful.', true); 
                $resOk = true;                
            } else {
                $this->logDebug('Yahoo_OAuth: Refresh unsuccessful - ' . $body_parsed['oauth_problem']);
                $resOk = false;
            }
         
        } else {
            $this->logDebug('Yahoo_OAuth: Unable to refresh token.');
        }
        
        return $resOk;
    }

    /**
     * Requests the verifier URL.  Verifier URL is used by the user to supply the
     * confirmation to Yahoo! services that they are giving this application
     * rights to access data.  The verifier should be updated with setRequestVerifier
     * in order to complete.
     * 
     * @return String verifierUrl
     * @throws Exception
     */
    public function getVerifierUrl() {        
        $response = $this->oauth->getRequestToken('https://api.login.yahoo.com/oauth/v2/get_request_token', 
                'oob', OAUTH_HTTP_METHOD_GET);
        $this->setRequestTokens($response['oauth_token'], 
                $response['oauth_token_secret']);
        $this->logDebug('Yahoo_OAuth: Completed request verifier Url.');
        return $response['xoauth_request_auth_url'];
    }

    /**
     * Initializes the connection by making a request to the
     * Access token.  This requires that the requestUrl was requested and that
     * the user has completed their confirmation.
     * 
     * @param String $verifier
     * @return Boolean
     * @throws Exception
     */
    public function requestAccessToken($requestToken, 
            $requestSecret, 
            $verifier = null, 
            $method = 'GET') {
        
        if ($verifier) {
            $this->requestVerifier = $verifier;
        }

        try {
            $this->oauth->setToken($requestToken, $requestSecret);
            $response = $this->oauth->getAccessToken('https://api.login.yahoo.com/oauth/v2/get_token', 
                    NULL, // auth session handle
                    $this->requestVerifier, // Verifier from previous login
                    $method);  // HTTP GET required 

            $this->setAccessTokens($response['oauth_token'], 
                    $response['oauth_token_secret'], 
                    $response['oauth_session_handle']);

            $this->logDebug('Yahoo_OAuth: Completed requesting Access Token.');
        } catch (Exception $ex) {
            $this->logDebug('Yahoo_OAuth: Access Token Exception - ' . $ex->getMessage());
            throw $ex;
        }
        
        return true;
    }

    /**
     * Log debug messages to the error log.  Each log entry contains the 
     * current access, secret and session.
     * 
     * @param String $msg
     */
    private function logDebug($msg, $tokens = false) {
        $this->oauthMessage = $msg;
        
        if ($this->debug) {
            error_log($msg);
            
            if ($tokens) {
                error_log('Access Token: ' . $this->accessToken);
                error_log('Access Secret: ' . $this->accessSecret);
                error_log('Access Session: ' . $this->accessSession);                
            }
        }
    }

    public function getLastResponse() {
        return $this->oauth->getLastResponse();
    }

    public function getAccessSecret() {
        return $this->accessSecret;
    }

    public function getAccessSession() {
        return $this->accessSession;
    }

    public function getAccessToken() {
        return $this->accessToken;
    }

    public function getRequestToken() {
        return $this->requestToken;
    }

    public function getRequestSecret() {
        return $this->requestSecret;
    }

    public function getRequestVerifier() {
        return $this->requestVerifier;
    }
    
    public function getOauthMessage() {
        return $this->oauthMessage;
    }

    /**
     * Set the access token fields.  Doing so also updates the OAuth Token
     * values.
     * @param String $token
     * @param String $secret
     * @param String $session
     */
    public function setAccessTokens($token, $secret, $session) {
        $this->accessToken = $token;
        $this->accessSecret = $secret;
        $this->accessSession = $session;
        
        $this->oauth->setToken($token, $secret);
    }

    /**
     * Set the request token fields.  If no Verifier is provided, then it
     * is set to a null value, expecting that a new verifier will be requested
     * using the new token set.
     * @param String $token
     * @param String $secret
     * @param String $verifier
     */
    public function setRequestTokens($token, $secret, $verifier = null) {
        $this->requestToken = $token;
        $this->requestSecret = $secret;
        $this->requestVerifier = $verifier;     
    }
}
