<?php

namespace YahooFantasySports;

use Exception;

/**
 * Handles the creation of menus and and other specific Administration
 * features required by the plugin.
 *
 * @author kendavidson
 */
class YahooFantasyAdmin {
    
    /**
     * Constructor
     * @param string $plugin
     * @param string $version
     */
    public function __construct($plugin, $version) {
        $this->pluginName = $plugin;
        $this->version = $version;
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
        add_action('admin_enqueue_styles', array(&$this,'enqueueStyles'));
        add_action('admin_enqueue_scripts', array(&$this,'enqueueScripts'));
        add_action('admin_menu', array(&$this, 'addMenu'));
        
        // Setup AJAX requests
        add_action('wp_ajax_get_consumer_keys', array(&$this, 'getConsumerKeys'));
        add_action('wp_ajax_save_consumer_keys', array(&$this, 'saveConsumerKeys'));
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
        
        
        
        if ( !current_user_can( $allowed ) ) {
            throw new Exception(
                    __('User does not have the permissions required to access this functionality.', 'yahoo-fantasy'));
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
            
            $key = get_option('yf_consumer_key');
            $secret = get_option('yf_consumer_secret');
            
            $response = array(
                'consumerKey'       => $key,
                'consumerSecret'    => $secret                
            );
            wp_send_json_success($response);
        } catch (Exception $ex) {
            wp_send_json_error($ex->getMessage());
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
            $this->checkSecurity('manage_options');
            
            $key = isset($_POST['consumerKey']) ? $_POST['consumerKey'] : '';
            $secret = isset($_POST['consumerSecret']) ? $_POST['consumerSecret'] : '';
            
            error_log("Attempting to update Yahoo! consumer keys {$key} and {$secret}.");
            
            $saveKey = update_option('yf_consumer_key', $key);
            if (!$saveKey && $key != get_option('yf_consumer_key')) {
                throw new Exception('Could not save Yahoo! Consumer Key.  See Wordpress debug log for more information.');
            }
            
            $saveKey = update_option('yf_consumer_secret', $secret);
            if (!$saveKey && $secret != get_option('yf_consumer_secret')) {
                throw new Exception('Could not save Yahoo! Consumer Secret.  See Wordpress debug log for more information.');
            }            
            
            $response = array(
                'consumerKey'       => $key,
                'consumerSecret'    => $secret
            );
            wp_send_json_success($response);                        
        } catch (Exception $ex) {         
            wp_send_json_error($ex->getMessage());                               
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
        echo '  <yahoo-fantasy-admin-settings />';
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
        echo '  <yahoo-fantasy-admin-keys />';    
        echo '</div>';
    }
}
