<?php
namespace YahooFantasySports\Internationalize;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @author     Bob Webster
 * @author     Kenneth Davidson
 * @since      2.0.0
 */
class Yahoo_Fantasy_i18n {

    public function __construct($plugin, $version) {
        $this->pluginName = $plugin;
        $this->version = $version;
        $this->init();
    }
    
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    private function init() {
        load_plugin_textdomain(
                'yahoo-fantasy', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }

}