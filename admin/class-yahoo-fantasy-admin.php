<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    yahoo-fantasy
 * @subpackage yahoo-fantasy/admin
 * 
 * @author     Bob Webster <bwebster@azionebi.com>
 * @author     Ken Davidson <ken.j.davidson@live.ca>
 */
class Yahoo_Fantasy_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $Yahoo_Fantasy    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name   The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;            
        $this->init();
    }
    
    /**
     * Initialized the required items for the Yahoo_Fantasy_Admin class, this
     * includes all Action, Filter and Event features.
     */
    protected function init() {
        
        // Add the Scripts and Styles 
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        
        // Add the administration menu callback and the register settings
        add_action('admin_menu', array(&$this, 'add_menu'));
        add_action('admin_init', array(&$this, 'register_yfsettings'));
        
        // Add the Admin AJAX method handling
    }

    /**
     * Register the style sheets for the Administration area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {

        wp_enqueue_style($this->plugin_name, 
                plugin_dir_url(__FILE__) . 'css/yahoo-fantasy-admin.css', 
                array(), 
                $this->version, 
                'all');
    }

    /**
     * Register the JavaScript for the Administration area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {

        wp_enqueue_script($this->plugin_name, 
                plugin_dir_url(__FILE__) . 'js/yahoo-fantasy-admin.js', 
                array('jquery'), 
                $this->version, 
                false);
    }
    
    /**
     * Add the menu options to the administration page.  Yahoo Fantasy Settings
     * added to the main menu.
     * 
     * @since 1.0.0
     */
    public function add_menu() {
        add_menu_page('Yahoo Fantasy Settings', 
                'Yahoo Fantasy', 
                'manage_options', 
                'yahoo_fantasy_settings', 
                array(&$this, 'plugin_settings_page'));
        
        add_submenu_page('yahoo_fantasy_settings', 
                'Yahoo OAuth Configuration', 
                'Yahoo OAuth', 
                'manage_options', 
                'yahoo_oauth', 
                array(&$this, 'plugin_settings_subpage'));
        
        add_submenu_page('yahoo_fantasy_settings', 
                'Customization', 
                'Customization', 
                'manage_options', 
                'yahoo_custom', 
                array(&$this, 'plugin_custom_subpage'));
    }

    /**
     * Validate the logged in user
     * 
     * @since 1.0.0
     */
    public function plugin_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'yahoo-fantasy'));
        }
        include('partials/yahoo-fantasy-admin-about-display.php');
    }

    /**
     * Validate the logged in user, then display the OAuth configuration
     * admin page.
     * 
     * @since 1.0.0
     */
    public function plugin_settings_subpage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'yahoo-fantasy'));
        }
        include('partials/yahoo-fantasy-admin-oauth-display.php');
    }
    
    /**
     * Validate the logged in user, then display the customization Admin page
     * 
     * @since 1.1.0
     */
    public function plugin_custom_subpage() {
         if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'yahoo-fantasy'));
        }
        include('partials/yahoo-fantasy-admin-customize-display.php');       
    }

    /**
     * Register the Yahoo! Fantasy Wordpress settings.  These settings are used to store
     * all the required OAuth values used connect to the Yahoo! services.
     * 
     * @since 1.0.0
     */
    public function register_yfsettings() {
        $settings_group = 'yfoption-group';
        register_setting($settings_group, 'yf_consumer_key');
        register_setting($settings_group, 'yf_consumer_secret');
        register_setting($settings_group, 'yf_access_token');
        register_setting($settings_group, 'yf_access_secret');
        register_setting($settings_group, 'yf_access_session');
        register_setting($settings_group, 'yf_request_verifier');
        register_setting($settings_group, 'yf_request_token');
        register_setting($settings_group, 'yf_request_secret');
    }    

}
