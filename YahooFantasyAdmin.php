<?php

namespace YahooFantasySports;

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
        add_action('wp_ajax_get_keys', array(&$this, 'getOAuthKeys'));
        add_action('wp_ajax_save_keys', array(&$this, 'saveOAuthKeys'));
    }
    
    /**
     * Handles requests for getting the OAuth keys from the Wordpress plugin
     * options.  This is called by the get_keys Ajax call, but can be called
     * from anywhere in the Plugin.
     */
    public function getOAuthKeys() {
        
    }
    
    /**
     * Handles requests for saving the OAuth keys to the Wordpress plugin 
     * options.  This is called by the save_keys Ajax call.
     */
    public function saveOAuthKeys() {
        
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
