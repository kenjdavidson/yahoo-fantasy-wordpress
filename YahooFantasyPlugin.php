<?php
namespace YahooFantasySports;

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.1.0
 * @package           Yahoo_Fantasy
 *
 * @wordpress-plugin
 * Plugin Name:       Yahoo Fantasy Stats
 * Plugin URI:        http://azionebi.com/yahoo-fantasy/
 * Description:       Display Yahoo Sports Fantasy information within your Wordpress site.  Currently supports displaying games, leagues, standings and teams using the [yahoofantasysports] shortcode.
 * Version:           1.1.0
 * Author:            Bob Webster
 * Author URI:        http://azionebi.com
 * Author:            Ken Davidson
 * Author URI:        http://kenjdavidson.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yahoo-fantasy
 * Domain Path:       /languages
 */

require dirname( __FILE__ ) . 'YahooFantasySports.php';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function provides error log output when the WP_DEBUG variable is set to
 * true.  Logging function is available through the rest of the included
 * and required files.
 * 
 * @param String $msg
 */
function yahooSportsLogger($msg) {
    if (WP_DEBUG) {
        error_log('Yahoo_Fantasy plugin: ' . $msg);
    }
}

// Register the activate and deactivate hooks to the YahooFantasySports class
register_activation_hook( __FILE__, array('YahooFantasyPlugin', 'activate') );
register_deactivation_hook( __FILE__, array('YahooFantasyPlugin', 'deactivate') );

// Begin execution of the plugin by creating a Global variable and calling
// it's run() method.  The Yahoo_Fantasy class is responsible for determining
// whether the public or admin functionality should be available.
$yfPlugin = new YahooFantasySports;

/**
 * YahooFantasyPlugin class
 * 
 * Instantiate the Wordpress plugin
 */
class YahooFantasyPlugin {
    
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
        $this->pluginName = 'yahoo-fantasy';
        $this->version = '1.0.0';        

        $this->init();
    }
    
    /**
     * Initialized the Yahoo_Fantasy Object by setting the appropriate 
     * Actions, Filters and Events.
     */
    protected function init() {
        if (is_admin()) {
            
        }
        
        $this->yahooI18n = new \YahooFantasySports\International\YahooFantasyI18n();
        $this->yahooApi = new \YahooFantasySports\YahooFantasySports();
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
        
    }
    
    /**
     * Deactivates the plugin.
     * 
     * Responsible for cleaning up custom database tables and configuration
     * items.  Removes user configuration settings.
     */
    public static function deactivate() {
        
    }
}
