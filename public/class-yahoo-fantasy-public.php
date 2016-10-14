<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-specific stylesheet and JavaScript.
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/public
 * @author     Bob Webster <bwebster@azionebi.com>
 */
class Yahoo_Fantasy_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $Yahoo_Fantasy;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->Yahoo_Fantasy, plugin_dir_url( __FILE__ ) . 'css/yahoo-fantasy-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->Yahoo_Fantasy, plugin_dir_url( __FILE__ ) . 'js/yahoo-fantasy-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Execute the data output.
	 *
	 * @since    1.0.0
	 */
	public function write_stats() {

            /**
             * Write the Yahoo Fantasy Stats.
             *
             * Description
             * 
             *
             * @since 1.0.0
             *
             * @param 
             * @return 
             */
            
                $yf_content = $this->get_yahoo_fantasy_data();
        	include_once( 'partials/yahoo-fantasy-public-display.php' );

	}

	/**
	 * Execute the data output.
	 *
	 * @since    1.0.0
	 */
	public function get_yahoo_fantasy_data() {

                /**
                 * Retrieve response data from Yahoo Fantasy API
                 *
                 * Authenticates API via oAuth 2-legged process
                 * Call API for response
                 *
                 * @since 1.0.0
                 *
                 * @param 
                 * @return XML  $xml    XML Response Data
                 */

                $consumer_key = get_option( 'yf_consumer_key' );
                $consumer_secret = get_option( 'yf_consumer_secret' );

                $xml = NULL;
                
                $o = new OAuth( $consumer_key, $consumer_secret,
                                OAUTH_SIG_METHOD_HMACSHA1,
                                OAUTH_AUTH_TYPE_URI );

                $url = get_option( 'yf_url' );

                try {
                  if( $o->fetch( $url ) ) {

                    $xml = simplexml_load_string($o->getLastResponse(), "SimpleXMLElement", LIBXML_NOCDATA);
                    return $xml;
                    //Successful fetch
                  } else {
                    print "Couldn't fetch<br>";
                    return;
                  }
                } catch( OAuthException $e ) {
                  print 'Error: ' . $e->getMessage() . "<br>";
                  print 'Response: ' . $e->lastResponse . "<br>";

                }
        
        }
}
