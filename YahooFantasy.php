<?php

namespace YahooFantasySports;

require dirname( __FILE__ ) . '/YahooFantasyAdmin.php';
require dirname( __FILE__ ) . '/YahooFantasyRestHandler.php';
require dirname( __FILE__ ) . '/YahooFantasyShortcodeHandler.php';
require dirname( __FILE__ ) . '/vendor/autoload.php';

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

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

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
        
        $isAdmin = false;
        if (is_admin()) {
            $isAdmin = true;
            
            add_action('admin_enqueue_scripts', array($this,'enqueueScripts'));
            
            $this->admin = new \YahooFantasySports\YahooFantasyAdmin($this->pluginName, $this->version);
        }
        
        $this->shortcodes = new \YahooFantasySports\YahooFantasyShortcodeHandler($this->pluginName, $this->version, $isAdmin);
        $this->rest = new \YahooFantasySports\YahooFantasyRestHandler($this->pluginName, $this->version, $isAdmin);                              
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function getPluginName() {
        return $this->pluginName;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function getVersion() {
        return $this->version;
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
        
        $ngConfig = [
            'nonce'     => wp_create_nonce(YahooFantasy::NONCE_ACTION),
            'base_url'  => plugin_dir_url(__FILE__),
            'ajax_url'  => admin_url( 'admin-ajax.php' ),
            'text'      => array(
                'cannot_get_keys'   => __('Cannot retrieve OAuth keys. Check Wordpress debug log for info.', 'yahoo-fantasy'),
                'cannot_save_keys'   => __('Cannot save OAuth keys. Check Wordpress debug log for info.', 'yahoo-fantasy'),
                'saved_keys'    => __('Successfully saved Yahoo! OAuth Consumer Keys.', 'yahoo-fantasy')
            )
        ];
        wp_localize_script('yfsbootstrap', 'wp_yahoo_fantasy_plugin', $ngConfig);
    }
   
}