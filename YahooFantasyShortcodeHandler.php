<?php
namespace YahooFantasySports;

/**
 * YahooFantasyShortcodeHandler
 * 
 * Associate and handle the Shortcodes.  A number of short codes are available
 * that will display a specific set of Angular elements:
 * 
 * <yahoo-fantasy-leagues>
 * <yahoo-fantasy-teams>
 * <yahoo-fantasy-standings>
 * <yahoo-fantasy-rosters>
 * 
 * @author Kenneth Davidson
 * @version 2.0.0
 */
class YahooFantasyShortcodeHandler {
    
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
    public function __construct($plugin, $version, $admin) {             
        $this->pluginName = $plugin;
        $this->version = $version;
        $this->isAdmin = $admin;
        //$this->init();
    }    
    
}