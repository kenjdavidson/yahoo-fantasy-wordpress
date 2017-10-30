<?php
namespace YahooFantasySports;

use \Exception;

/**
 * YahooFantasyRESTHandler
 * 
 * Adds the REST/AJAX functionality and handling to the Wordpress callbacks.
 * 
 * @author Kenneth Davidson
 * @version 2.0.0
 */
class YahooFantasyRestHandler {
    
    /**
     * ACTION_SECURITY array used to perform appropriate lookup of
     * required user security based on the request made.
     */
    const ACTION_SECURITY = array(
        'yf_get_consumer_keys'      => 'manage_options',
        'yf_save_consumer_keys'     => 'manage_options',
        'yf_get_user_account'       => 'publish_posts',
        'yf_request_auth'           => 'publish_posts',
        'yf_logout'                 => 'publish_posts'
    );
    
    /**
     * YahooFantasy plugin reference
     * 
     * @var YahooFantasy
     */
    protected $plugin;   
    
    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     * 
     * If the current request is for an admin page, then the admin menu and
     * admin init hooks are used to register the settings.
     *
     * @since    1.0.0
     */
    public function __construct($plugin) {             
        $this->plugin = $plugin;
        $this->init();
    }
    
    /**
     * Initialized the Yahoo_Fantasy Object by setting the appropriate 
     * Actions, Filters and Events.
     */
    protected function init() {
        if ($this->plugin->isAdmin()) {
            // Setup AJAX requests
            add_action('wp_ajax_yf_get_consumer_keys', array(&$this, 'handleAdminAjax'));
            add_action('wp_ajax_yf_save_consumer_keys', array(&$this, 'handleAdminAjax'));           
            add_action('wp_ajax_yf_request_auth', array(&$this, 'handleAdminAjax'));
            add_action('wp_ajax_yf_logout', array(&$this, 'handleAdminAjax'));
            
            add_action('wp_ajax_yf_get_user_account', array(&$this, 'handleAjaxNopriv'));
            add_action('wp_ajax_yf_get_user_games', array(&$this, 'handleAjaxNopriv'));
            add_action('wp_ajax_yf_get_user_leagues', array(&$this, 'handleAjaxNopriv'));
            add_action('wp_ajax_yf_get_user_teams', array(&$this, 'handleAjaxNopriv'));
        }
        
        add_action('wp_ajax_nopriv_yf_get_user_account', array(&$this, 'handleAjaxNopriv'));
        add_action('wp_ajax_nopriv_yf_get_user_games', array(&$this, 'handleAjaxNopriv'));
        add_action('wp_ajax_nopriv_yf_get_user_leagues', array(&$this, 'handleAjaxNopriv'));
        add_action('wp_ajax_nopriv_yf_get_user_teams', array(&$this, 'handleAjaxNopriv'));
    }    
    
    /**
     * Handle all Admin Ajax requests.  Security, User security and
     * actions are checked.
     */
    public function handleAdminAjax() {
        $action = filter_input(INPUT_GET, 'action');
        
        try {            
            $this->checkSecurity(YahooFantasyRestHandler::ACTION_SECURITY[$action]); 
            
            /* @var $provider YahooFantasyProvider */
            $provider = $this->plugin->getYahooProvider();  

            /* @var $service YahooFantasyService */
            $userId = get_current_user_id();
            $service = $this->plugin->getYahooService($provider, $userId); 
            
            if (!$service) {
                wp_send_json_error(array(
                    'errorType'     => 'Exception',
                    'errorCode'     => $provider->getAuthorizationUrl(),
                    'errorMessage'  => 901
                ));                             
            }
            
            $callable = YahooFantasyRestHandler::callableFromAction($action);
            $response = call_user_func(array($this, $callable));  
            wp_send_json_success($response);    

        } catch (Exception $ex) {
            error_log("Action {$action} threw an exception: " . $ex->getMessage());
            
            wp_send_json_error(array(
                'errorType'     => 'Exception',
                'errorCode'     => $ex->getCode(),
                'errorMessage'  => $ex->getMessage()
            )); 
        }        
    }
    
    /**
     * Handle all No Priv Ajax requests.  Security and actions are checked,
     * but user security is not checked.  No priv functions require a 
     * userId to be provide, if it's not provided then an exception is thrown
     * as we can't lookup details. 
     */
    public function handleAjaxNopriv() {
        $action = filter_input(INPUT_GET, 'action');
        $userId = filter_input(INPUT_GET, 'userId');
        $params = $this->filterParams();
        
        if (is_admin()) {
            $userId = get_current_user_id();
        }                
        
        try {                                       
            if (!$userId) {
                throw new Exception(__('UserId is required in order to lookup Yahoo! Resources.', 'yahoo-fantasy'), 902);
            }   
            
            $this->checkSecurity(null);               
            
            /* @var $provider YahooFantasyProvider */
            $provider = $this->plugin->getYahooProvider();  

            /* @var $service YahooFantasyService */
            $service = $this->plugin->getYahooService($provider, $userId); 
            
            if (!$service && $provider) {
                throw new Exception($provider->getAuthorizationUrl(), 901);
            } else if (!$service) {
                throw new Exception(__('Unable to access Yahoo! services for this user.', 'yahoo-fantasy'));
            }
            
            $callable = YahooFantasyRestHandler::callableFromAction($action);
            error_log("Action {$action} making request to {$callable}");
            $response = call_user_func_array(array($this, $callable), 
                    array($service, $userId, $params));  
            wp_send_json_success($response);             
                        
        } catch (Exception $ex) {
            error_log("Action {$action} threw an exception: " . $ex->getMessage());
            
            wp_send_json_error(array(
                'errorType'     => 'Exception',
                'errorCode'     => $ex->getCode(),
                'errorMessage'  => $ex->getMessage()
            )); 
        }        
    }
    
    /**
     * Method to return consistent GET paramters for looking up user data
     * @return Array
     */
    private function filterParams() {
        return array(
            'seasons'   => filter_input(INPUT_GET, 'seasons') ? filter_input(INPUT_GET, 'seasons') : date('Y'),
            'key'       => filter_input(INPUT_GET, 'key') ? filter_intput(INPUT_GET, 'key') : null
        );
    }
    
    /**
     * Converts the action provide (yf_do_something) to a callable name
     * (doSomething) which will be used in this service.
     * @param string $action
     */
    private function callableFromAction($action) {
        $patterns = array('(yf_)','(_\w)');
        $callable = preg_replace_callback($patterns, function($match){
                if ($match[0] == 'yf_') return '';
                return strtoupper(substr($match[0], 1));
            }, $action);            
        return $callable; 
    }
    
    /**
     * Performs security checking of the Ajax request based on the Nonce for
     * the YahooFantasy plugin, as well as the allowed permissions.
     * @param type $allowed
     */
    public function checkSecurity($allowed) {        
        if ( !check_ajax_referer( YahooFantasy::NONCE_ACTION, 'security' ) ) {
            throw new Exception(
                    __('Max time exceeded on Ajax requests.  Please refresh the page and try again.', 'yahoo-fantasy'));
        }
                        
        if ( isset($allowed) && !current_user_can( $allowed ) ) {
            throw new Exception(
                    __('User does not have the permissions required to access this functionality.', 'yahoo-fantasy'));
        }
    }
    
    /**
     * Handles the request to retrieve the User Games.
     * @param YahooFantasyService $service
     * @param int $userId
     * @param Array $params
     * @return mixed array or Exception based on service response
     */
    public function getUserGames($service, $userId, $params) {
        try {
            $games = $service->getUserGames($params['seasons']);
            error_log("Found games for {$userId}: " . print_r($games, true));

            return array(
                'games'      => $games
            );            
        } catch (Exception $ex) {
            return $ex;
        }
    }

    /**
     * Handles the request for leagues
     * @param YahooFantasyService $service
     * @param int $userId
     * @param Array $params
     * @return mixed array or Exception based on service response
     */
    public function getUserLeagues($service, $userId, $params) {
        try {
            $leagues = $service->getUserLeagues($params['seasons']);
            error_log("Found leagues for {$userId}: " . print_r($leagues, true));

            return array(
                'leagues'      => $leagues
            );             
        } catch (Exception $ex) {
            return $ex;
        }       
    }
    
    /**
     * 
     * @param type $service
     * @param type $userId
     * @param type $params
     * @return mixed array or Exception based on service response
     */
    public function getUserTeams($service, $userId, $params) {
        try {
            $teams = $service->getUserTeams($params['seasons']);
            error_log("Found leagues for {$userId}: " . print_r($teams, true));

            return array(
                'teams'      => $teams
            );             
        } catch (Exception $ex) {
            return $ex;
        }           
    }
    
    /**
     * Handles a logout of the user.  This just deletes the currently logged
     * in user option yf_access_token.  
     */
    public function logout() {
        $deleted = $this->plugin->saveYahooOption('yf_access_token', null, true);
        wp_send_json_success();
        
        if (!$deleted) {
            throw new Exception("Unable to clear users access token.". 902);
        }
    }
    
    /**
     * Handles requests for getting the user account.  There are three options 
     * available from this request:
     * - The account is returned successfully, a JSON object of the user details
     *   is returned to the caller.
     * - The account is not returned but the Consumer keys are setup, in this case
     *   the request Link is returned.
     * - The Consumer keys are not setup (or some other error has occurred) in
     *   this case the error JSON is returned.
     * 
     * This function requires the 'publish_posts' security.
     * 
     * @param YahooFantasyService $service
     */
    public function getUserAccount($service) {
        try {                   
            $account = $service->getUSerAccount();
            error_log("Found user account for service" . print_r($account, true));
            
            return array(
                'account'      => $account
            );            
        } catch (Exception $ex) {
            return $ex;
        }
    }
    
    /**
     * Handles requests for getting the OAuth keys from the Wordpress plugin
     * options.  This is called by the get_keys Ajax call, but can be called
     * from anywhere in the Plugin.   Like saving, only users with 
     * 'manage_options' have access to get the Consumer keys
     */
    public function getConsumerKeys() {
        try {
            $this->checkSecurity('manage_options');
            $keys = $this->plugin->getConsumerKeys();

            return array(
                'consumerKey'       => $keys['clientId'],
                'consumerSecret'    => $keys['clientSecret'],
                'redirectUri'       => $keys['redirectUri']
            );            
        } catch (Exception $ex) {
            return $ex;
        }     
    }
     
    /**
     * Handles requests for saving the OAuth keys to the Wordpress plugin 
     * options.  This is called by the save_keys Ajax call.  Saving consumer
     * keys is only allowed by users that have 'manage_options' permissions
     * on the site.
     */
    public function saveConsumerKeys() { 
        try {
            $key = filter_input(INPUT_POST, 'consumerKey');
            $secret = filter_input(INPUT_POST, 'consumerSecret');

            $redirect = filter_input(INPUT_POST, 'redirectOob');            
            $redirectUrl = ("false" != $redirect) 
                    ? 'oob' : get_site_url(null, '/wp-admin/admin.php?page=yahoo_fantasy_keys');

            error_log("Attempting to update Yahoo! consumer keys {$key}, {$secret}, {$redirectUrl}.");

            $this->plugin->saveYahooOption('yf_consumer_key', $key);
            $this->plugin->saveYahooOption('yf_consumer_secret', $secret);
            $this->plugin->saveYahooOption('yf_redirect_url', $redirectUrl);

            return array(
                'consumerKey'       => $key,
                'consumerSecret'    => $secret,
                'redirectUrl'       => $redirect
            );             
        } catch (Exception $ex) {
            return $ex;
        }                               
    }
    
    /**
     * RequestAuth is used to take the Code provided by the user and request
     * the AccessToken.  If supplied the AccessToken is saved to the User 
     * options as a JSON String, if not an exception is thrown.
     */
    public function requestAuth() {
        try {
            $code = filter_input(INPUT_POST, 'authCode');

            error_log('Attempting to request AccessToken with code ' . $code);

            $provider = $this->plugin->getYahooProvider();
            $token = $provider->getAccessToken('authorization_code', [
                'code'  => $code
            ]);

            error_log('Retrieved AccessToken: ' . json_encode($token));

            $this->plugin->saveYahooOption('yf_access_token', 
                    json_encode($token), true);

            return array();            
        } catch (Exception $ex) {
            return $ex;
        }
    }    
}
