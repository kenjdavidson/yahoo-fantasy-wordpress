<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includesÏ
 */

require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-yahoo-oauth.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-yahoo-sports-api.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-yahoo-fantasy-i18n.php';

require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-yahoo-fantasy-admin.php';
require_once plugin_dir_path(dirname(__FILE__)) . 'public/YahooFantasyPublic.php';

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includes
 * @author     Bob Webster <bwebster@azionebi.com>
 * @author     Ken Davidson <ken.j.davidson@live.ca>
 */
class Yahoo_Fantasy {

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $Yahoo_Fantasy    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;
    
    /**
     * Yahoo Fantasy Public object used throughout the Wordpress cycle
     * @var YahooFantasyPublic $yahooPublic
     */
    protected $yahooPublic;
    
    /**
     * Yahoo Fantasy Admin object used throughout the Wordpress cycle
     * @var Yahoo_Fantasy_Admin $yahooAdmin
     */
    protected $yahooAdmin;
    
    /**
     * Yahoo Fantasy Internationalization object
     * @var Yahoo_Fantasy_i18n $yahooI18n
     */
    protected $yahooI18n;

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
        $this->plugin_name = 'yahoo-fantasy';
        $this->version = '1.0.0';        
        $this->yahooI18n = new Yahoo_Fantasy_i18n();
        $this->init();
    }
    
    /**
     * Initialized the Yahoo_Fantasy Object by setting the appropriate 
     * Actions, Filters and Events.
     */
    protected function init() {
        if (is_admin()) {
            yahooSportsLogger('Creating Yahoo_Fantasy_Admin Object.');
            $this->yahooAdmin = new Yahoo_Fantasy_Admin(
                    $this->plugin_name, $this->version);           
        } else {
            yahooSportsLogger('Creating YahooFantasyPublic Object.');
            $this->yahooPublic = new YahooFantasyPublic(
                    $this->plugin_name, $this->version);
        }        
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->Yahoo_Fantasy;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
