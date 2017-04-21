<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           Yahoo_Fantasy
 *
 * @wordpress-plugin
 * Plugin Name:       Yahoo Fantasy Stats
 * Plugin URI:        http://azionebi.com/yahoo-fantasy/
 * Description:       Display Yahoo Sports Fantasy information within your Wordpress site.
 * Version:           1.0.0
 * Author:            Azione Business Intelligence adapted from the WordPress Boilerplate Plugin
 * Author URI:        http://azionebi.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yahoo-fantasy
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function provides error log output when the WP_DEBUG variable is set to
 * true.
 * @param String $msg
 */
function yahooSportsLogger($msg) {
    if (WP_DEBUG) {
        error_log('Yahoo_Fantasy plugin: ' . $msg);
    }
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_Yahoo_Fantasy() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yahoo-fantasy-activator.php';
	Yahoo_Fantasy_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_Yahoo_Fantasy() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-yahoo-fantasy-deactivator.php';
	Yahoo_Fantasy_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Yahoo_Fantasy' );
register_deactivation_hook( __FILE__, 'deactivate_Yahoo_Fantasy' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-yahoo-fantasy.php';

// Begin execution of the plugin by creating a Global variable and calling
// it's run() method.  The Yahoo_Fantasy class is responsible for determining
// whether the public or admin functionality should be available.
yahooSportsLogger('Creating Yahoo_Fantasy Object');
$yfPlugin = new Yahoo_Fantasy;
