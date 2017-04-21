<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/includes
 * @author     Bob Webster <bwebster@azionebi.com>
 * @author     Ken Davidson <ken@kenjdavidson.com>
 */
class Yahoo_Fantasy_Deactivator {

	/**
	 * Deactivate the plugin
	 *
	 * When deactivating the plugin, all the options are deleted from
         * the Wordpress settings.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
            delete_option('yf_consumer_key');
            delete_option('yf_consumer_secret');
            delete_option('yf_access_token');
            delete_option('yf_access_secret');
            delete_option('yf_access_session');
            delete_option('yf_request_verifier');
            delete_option('yf_request_token');
            delete_option('yf_request_secret');            
	}

}
