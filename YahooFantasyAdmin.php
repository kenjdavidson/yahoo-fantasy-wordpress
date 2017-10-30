<?php

namespace YahooFantasySports;

/**
 * Handles the creation of menus and and other specific Administration
 * features required by the plugin.
 *
 * @author kendavidson
 */
class YahooFantasyAdmin {
    
    /* @var $plugin YahooFantasy */
    private $plugin;
    
    /**
     * Constructor
     * @param string $plugin
     * @param string $version
     */
    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->init();
    }  
    
    /**
     * Initialized the Yahoo_Fantasy Object by setting the appropriate 
     * Actions, Filters and Events.  The following are completed during an
     * administration init phase:
     * 
     * Add Menus    All menus are added to the administration screen.  The menus
     *              available are:
     *              - Yahoo Fantasy which displays an about screen
     *              - Yahoo OAuth which allows the user to enter the Key/Secret
     *              - User Login which provides different users to allow access
     *              - Customization allows for customized API calls
     */
    protected function init() {
        add_action('admin_enqueue_scripts', array(&$this->plugin,'enqueueScripts'));
        add_action('admin_menu', array(&$this, 'addMenu'));
             
        // Handle OAuth return code if available
        if (filter_input(INPUT_GET, 'code')) {
            add_action( 'init', array(&$this, 'handleRedirectAuth'));
        }
    }    
    
   /**
     * Handles the redirection request from the Yahoo! OAuth service.  This can happen
     * with live pages where the authentication comes back as normal.  This 
     * is required because the wp_get_current_user_id functions are now
     * loaded.
     */
    public function handleRedirectAuth() {
        $code = filter_input(INPUT_GET, 'code');

        try {
            error_log('Attempting to request AccessToken with code ' . $code);

            $provider = $this->plugin->getYahooProvider();
            $token = $provider->getAccessToken('authorization_code', [
                'code'  => $code
            ]);

            $encoded = json_encode($token);
            error_log('Retrieved AccessToken: ' . $encoded);
            $this->plugin->saveYahooOption('yf_access_token', $encoded, true);                
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        } 
    }    
    
    /**
     * Enqueue the scripts related to the plugin
     */
    public function enqueueScripts() {
      
    }
    
    /**
     * Enqueue the styles related to the plugin
     */
    public function enqueueStyles() {
     
    }     
    
    /**
     * Add the menu options to the administration page.  Yahoo Fantasy Settings
     * added to the main menu.  This creates the Yahoo Fantasy settings 
     * menu hierarchy.
     */
    public function addMenu() {
        add_menu_page('Yahoo Fantasy Settings', 
                'Yahoo Fantasy', 
                'publish_posts', 
                'yahoo_fantasy_settings', 
                array(&$this, 'showSettingsPage'));
        
        add_submenu_page('yahoo_fantasy_settings',
                'Yahoo OAuth Keys',
                'OAuth Keys',
                'manage_options',
                'yahoo_fantasy_keys',
                array(&$this, 'showKeysPage'));
    }
    
    /**
     * The settings page provides information about the plugin and possible
     * options.  It outlines what is required to install, configure and 
     * show the Yahoo Fantasy Plugin details.
     * 
     * This page can be seen by anyone that can publish_posts
     */
    public function showSettingsPage() {        
        
        if (!current_user_can('publish_posts')) {
            wp_die(__('Publish_Post user access is required in order to View this page.', 'yahoo-fantasy'));
        }
        
        echo '<div class="yahoo-fantasy-plugin">';
        echo '  <yf-user-account />';
        echo '</div>';       
    }
    
    /**
     * The OAuth Keys page allows the site administrator to add their site
     * specific Yahoo Consumer and Secret keys to the Wordpress domain.
     * 
     * This page can only be seen by those that can manage_options
     */
    public function showKeysPage() {        
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Manage_Options user access is required in order to View this page.', 'yahoo-fantasy'));
        }
        
        echo '<div class="yahoo-fantasy-plugin">';
        echo '  <yf-oauth-admin />';    
        echo '</div>';
    }
}
