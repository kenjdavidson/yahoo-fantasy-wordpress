<?php

namespace YahooFantasySports;

require dirname( __FILE__ ) . '/YahooFantasyAdmin.php';
require dirname( __FILE__ ) . '/YahooFantasyRestHandler.php';
require dirname( __FILE__ ) . '/YahooFantasyShortcodeHandler.php';
require dirname( __FILE__ ) . '/vendor/autoload.php';

use Kenjdavidson\OAuth2\YahooFantasySports\Provider\YahooFantasyProvider;
use Kenjdavidson\OAuth2\YahooFantasySports\Provider\Service\YahooFantasyService;
use League\OAuth2\Client\Token\AccessToken;
use Exception;

/**
 * YahooFantasy class
 * 
 * Instantiate the Wordpress plugin
 */
class YahooFantasy {
    
    /**
     * Nonce action
     */
    const NONCE_ACTION = 'yf_wordpress-plugin';
    
    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $Yahoo_Fantasy    The string used to uniquely identify this plugin.
     */
    protected $pluginName;
    public function getPluginName() { return $this->pluginName; }

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;
    public function getVersion() { return $this->version; }
    
    /**
     * Whether the current run as an administrator
     * 
     * @since   2.0.0
     * @access  protected
     * @var     string  $isAdmin    Whether this is the current admin
     */
    protected $isAdmin = false;
    public function isAdmin() { return $this->isAdmin; }

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
    public function __construct() {
        $this->pluginName = 'yahoo-fantasy';
        $this->version = '2.0.0';        

        $this->init();
    }
    
    /**
     * Initialized the Yahoo_Fantasy Object by setting the appropriate 
     * Actions, Filters and Events.  if this is an administration login / page
     * then we have to add the Admin menus.
     */
    protected function init() {           

        add_action('wp_enqueue_scripts', array($this,'enqueueScripts'));
        
        if (is_admin()) {
            add_action('admin_enqueue_scripts', array($this,'enqueueScripts'));
                        
            $this->isAdmin = true;           
            $this->admin = new \YahooFantasySports\YahooFantasyAdmin($this);
        }
        
        $this->shortcodes = new \YahooFantasySports\YahooFantasyShortcodeHandler($this);
        $this->rest = new \YahooFantasySports\YahooFantasyRestHandler($this);                              
    }

    /**
     * Activates the plugin.
     * 
     * Responsible for creating custom tables and storing default configuration
     * settings.  Currently the only settings are the request and access tokens
     * saved by user id.
     */
    public static function activate() {
        
        // Add the site Consumer and Secret options
        add_option('yf_consumer_key');
        add_option('yf_consumer_secret');
        
        // Create the user Token table
    }
    
    /**
     * Deactivates the plugin.
     * 
     * Responsible for cleaning up custom database tables and configuration
     * items.  Removes user configuration settings.
     */
    public static function deactivate() {
        
        // Delete the options
        delete_option('yf_consumer_key');
        delete_option('yf_consumer_secret');
    }
    
    /**
     * Enqueue the scripts/styles related to the plugin.  This method is
     * also responsible for localizing the required scripts.
     * 
     * 1.   The JavaScript object 'wp_config' will be injected into the 
     *      yfsbootstrap providing it the location of the plugin.
     * 
     */
    public function enqueueScripts() {

        wp_enqueue_script('require',
                plugins_url('/app/require.js', __FILE__),
                array('jquery'));         
        
        wp_enqueue_script('yfsbootstrap',
                plugins_url('/app/yfs.boot.js', __FILE__),
                array('require'));             
        
        wp_enqueue_style('flexboxgrid', 
                plugins_url('/css/flexboxgrid.min.css', __FILE__),
                array(),
                '6.3.1'); 
        
        wp_enqueue_style('yahoo-fantasy',
                plugins_url('/css/yahoo-fantasy-plugin.css', __FILE__),
                array(),
                '1.0.0');
        
        wp_enqueue_style('toastr',
                'http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
        
        $ngConfig = [
            'nonce'         => wp_create_nonce(YahooFantasy::NONCE_ACTION),
            'base_url'      => plugin_dir_url(__FILE__),
            'ajax_url'      => admin_url( 'admin-ajax.php' ),
            'current_user'  => get_current_user_id(),
            'text'          => array()
        ];
        wp_localize_script('yfsbootstrap', 'wp_yahoo_fantasy_plugin', $ngConfig);
    }
    
    /**
     * Returns the site consumer keys for this Wordpress application
     * @return Array
     * @throws Exception 
     */
    public function getConsumerKeys() {
        $key = get_option('yf_consumer_key');            
        $secret = get_option('yf_consumer_secret');
        $redirect = get_option('yf_redirect_url');
        
        if (!$key || !$secret) {
            throw new Exception(__('Administor has not configured Yahoo Consumer Keys.', 'yahoo-fantasy'), 900);
        } 
        
        return array(
            'clientId'          => $key,
            'clientSecret'      => $secret,
            'redirectUri'       => $redirect
        );        
    }
   
    /**
     * Get the YahooFantasyProvider.  Performs a lookup of the consumer and
     * secret keys in the Wordpress options, if neither are found an exception
     * is thrown.
     * @return YahooFantasyProvider
     * @throws Exception
     */
    public function getYahooProvider() {
        $config = $this->getConsumerKeys();
        
        error_log('Creating YahooFantasyProvider with ' . print_r($config, true));
        return new YahooFantasyProvider($config);
    }

    /**
     * Get the YahooFantasyService
     * @param String $user
     * @throws Exception
     */
    public function getYahooService($provider, $user = null) {
        
        $tokenString = get_user_option('yf_access_token');            
        if (!$tokenString) {
            error_log('Invalid AccessToken, providing authorization url: ' . $provider->getAuthorizationUrl());
            return false;
        }      
        
        // Since we are here, we should actually have the login working
        // so we'll return the user details.
        $tokenJson = json_decode($tokenString, true); 
        $token = new AccessToken($tokenJson); 
        
        error_log("Creating Yahoo Fantasy Service with Access Token: " . print_r($token, true));
        $service = new YahooFantasyService($provider, $token, function($refreshed){
                $this->saveYahooOption('yf_access_token', json_encode($refreshed), true);
            });
            
        return $service;
    }
    
    /**
     * Function used to save Wordpress and Wordpress User options.
     * @param type $name
     * @param type $value
     * @param type $isUser
     * @param type $user
     */
    public function saveYahooOption($name, $value, $isUser = false, $user = null) {
        if (!$isUser) {
            error_log("Attempting to save Wordpress option {$name} with value {$value}.");
            $saved = update_option($name, $value);
            if (!$saved && $value != get_option($name)) {
                throw new Exception("Could not save Yahoo! Wordpress Option {$name} with value {$value}."
                . " See Wordpress logs.");
            }
        } else {
            $userId = ($user == null) ? get_current_user_id() : $user;
            
            error_log("Attempting to save user {$userId} option {$name} with value {$value}.");
            $saved = update_user_option($userId, $name, $value);
            if (!$saved && $value != get_option($name)) {
                error_log("Error saving user {$userId} option {$name} with status {$saved}.");
                throw new Exception("Could not save Yahoo! Wordpress User Option {$name} with value {$value}."
                . " See Wordpress logs.");
            }            
        }
        return $value;
    }
}