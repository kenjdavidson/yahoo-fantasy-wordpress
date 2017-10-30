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
 * @since             1.0.0
 * @package           Yahoo_Fantasy
 *
 * @wordpress-plugin
 * Plugin Name:       Yahoo Fantasy Stats
 * Plugin URI:        https://github.com/kenjdavidson/yahoo-fantasy
 * Description:       Display Yahoo Sports Fantasy information within your Wordpress site.  Currently supports displaying games, leagues, standings and teams using the [yahoofantasysports] shortcode.
 * Version:           2.0.0
 * Author:            Ken Davidson
 * Author URI:        http://kenjdavidson.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       yahoo-fantasy
 * Domain Path:       /languages
 */

require dirname( __FILE__ ) . '/YahooFantasy.php';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

////////////////////////////////////////////////////////////////////////////////
/// PLUGIN BEGINS HERE                                                       ///
////////////////////////////////////////////////////////////////////////////////

// Register the activate and deactivate hooks to the YahooFantasySports class
register_activation_hook( __FILE__, array('YahooFantasy', 'activate') );
register_deactivation_hook( __FILE__, array('YahooFantasy', 'deactivate') );

// Begin execution of the plugin by creating a Global variable and calling
// it's run() method.  The Yahoo_Fantasy class is responsible for determining
// whether the public or admin functionality should be available.
$yfPlugin = new YahooFantasy();

