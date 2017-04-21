<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includes
 * @author     Bob Webster <bwebster@azionebi.com>
 */
class Yahoo_Fantasy_i18n {

    public function __construct() {
        
    }
    
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        load_plugin_textdomain(
                'yahoo-fantasy', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}
