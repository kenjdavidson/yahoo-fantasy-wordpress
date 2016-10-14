<?php

/**
 * Register all shortcodes for the plugin
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includes
 */

/**
 * Register all shortcodes for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includes
 * @author     Bob Webster <bwebster@azionebi.com>
 */

function wp_Yahoo_Fantasy_shortcode( ) {

    $yahoo_fantasy = new Yahoo_Fantasy_Public( 'Yahoo_Fantasy', '1.0.0');

    $yahoo_fantasy->write_stats();
}
add_shortcode('yahoo-fantasy', 'wp_Yahoo_Fantasy_shortcode');
