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
 * @subpackage Yahoo_Fantasy/includes
 */

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
 */
class Yahoo_Fantasy {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Yahoo_Fantasy_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $Yahoo_Fantasy    The string used to uniquely identify this plugin.
	 */
	protected $Yahoo_Fantasy;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->Yahoo_Fantasy = 'yahoo-fantasy';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
                if ( is_admin() ){ 
                    add_action( 'admin_menu', array( &$this, 'add_menu' ) );
                    add_action( 'admin_init', array( &$this, 'register_yfsettings' ) );
                }


	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Yahoo_Fantasy_Loader. Orchestrates the hooks of the plugin.
	 * - Yahoo_Fantasy_i18n. Defines internationalization functionality.
	 * - Yahoo_Fantasy_Admin. Defines all hooks for the admin area.
	 * - Yahoo_Fantasy_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yahoo-fantasy-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yahoo-fantasy-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-yahoo-fantasy-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-yahoo-fantasy-public.php';

		/**
                 * The class responsible for defining all shortcodes that occur in the core plugin
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-yahoo-fantasy-shortcodes.php';

		$this->loader = new Yahoo_Fantasy_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Yahoo_Fantasy_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Yahoo_Fantasy_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Yahoo_Fantasy_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_yahoo_fantasy_menu', $plugin_admin, 'yahoo_fantasy_menu' );

}
/** Step 2 (from text above). */

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Yahoo_Fantasy_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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
        public function add_menu() {
            add_menu_page('Yahoo Fantasy Settings', 'Yahoo Fantasy', 'manage_options', 'yahoo_fantasy_settings', array(&$this, 'plugin_settings_page')); 
            add_submenu_page('yahoo_fantasy_settings', 'Yahoo Fantasy Settings', 'Yahoo Fantasy', 'manage_options', 'yahoo_fantasy_settings', array(&$this, 'plugin_settings_subpage')); 
        }

        public function plugin_settings_page() { 
            if(!current_user_can('manage_options')) { 
                wp_die(__('You do not have sufficient permissions to access this page.', 'yahoo-fantasy')); 
            }
            include(sprintf("%s/admin/partials/yahoo-fantasy-admin-display.php", plugin_dir_path( dirname( __FILE__ ) ))); 
        }

        public function plugin_settings_subpage() { 
            if(!current_user_can('manage_options')) { 
                wp_die(__('You do not have sufficient permissions to access this page.', 'yahoo-fantasy')); 
            }
//            include(sprintf("%s/admin/partials/yahoo-fantasy-admin-display.php", plugin_dir_path( dirname( __FILE__ ) ))); 
        }
        function register_yfsettings() { 
            $settings_group = 'yfoption-group';
            $setting_name = 'yf_url';
            register_setting( $settings_group, 'yf_consumer_key' );
            register_setting( $settings_group, 'yf_consumer_secret' );
            register_setting( $settings_group, 'yf_url' );
}        

}
