<?php
namespace YahooFantasySports;

require dirname( __FILE__ ) . 'Authentication/YahooOAuth1.php';
require dirname( __FILE__ ) . 'International/YahooFantasyI18n.php';

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
    public function __construct($plugin, $version) {             
        $this->pluginName = $plugin;
        $this->version = $version;
        $this->init();
    }
    
    /**
     * Initialized the Yahoo_Fantasy Object by setting the appropriate 
     * Actions, Filters and Events.
     */
    protected function init() {

    }
}
