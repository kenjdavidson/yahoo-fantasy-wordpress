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
 * @author     Ken Davidson <ken.j.davidson@live.ca>
 * 
 * @uses apply_filters('yahoo_sports_filter_request_error_text', String)
 * @uses apply_filters('yahoo_sports_filter_request_error_class', String)
 * @uses apply_filters('yahoo_sports_filter_parse_error_text', String)
 * @uses apply_filters('yahoo_sports_filter_parse_error_class', String)
 */
class Yahoo_Fantasy_Public {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;
    
    /**
     * Yahoo_OAuth object
     * 
     * @since 2.0.0
     * @var Yahoo_OAuth 
     */
    private $oauth;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version) {
        
        // Update the plugin name and version
        $this->plugin_name = $plugin_name;
        $this->version = $version;  
        $this->init();
        
        // Create the Yahoo_OAuth object used through the 
        $this->oauth = new Yahoo_OAuth(get_option('yf_consumer_key'), 
                get_option('yf_consumer_secret'), 
                get_option('yf_access_secret'), 
                get_option('yf_access_token'), 
                get_option('yf_access_session'), 
                get_option('yf_request_secret'));
        $this->oauth->setDebug(WP_DEBUG);        
    }

    /**
     * Register the style sheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, 
                plugin_dir_url(__FILE__) . 'css/yahoo-fantasy-public.css', 
                array(), 
                $this->version, 
                'all');
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script($this->plugin_name, 
                plugin_dir_url(__FILE__) . 'js/yahoo-fantasy-public.js', 
                array('jquery'), 
                $this->version, 
                false);
    }
    
    /**
     * Perform any final initialization.  This takes care of adding all the 
     * other Actions, Filters and Events that are needed to perform the public
     * display of the YahooFantasy plugin.
     * 
     */
    protected function init() {        
        // Styles and Scripts cannot be enqueued prior to the wp_enqueue_scripts
        // action.  Therefore we need to listen for this, and then use that
        // to call our functions to enqueue the actual files.
        yahooSportsLogger('Enqueuing Public Styles and Scripts.');
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_styles'));
        add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        
        // [yahoofantasysports] shortcode.
        yahooSportsLogger('Adding [yahoofantasysports] shortcode.');
        add_shortcode('yahoofantasysports', array(&$this, 'yahoo_fantasy_shortcode'));
    }

    /**
     * Handles all requests for the [yahoo-fantasy-sports] shortcode.  The short
     * code attributes determine what information is displayed.   The bare bones
     * shortcode displays the users game summary, a small list of sports in which
     * the user had played in for a particular season.  Some other display types
     * are:
     * 
     * - games: display a list of games (default)
     * - leagues: display a list of leagues
     * - matchups: display a list of matchups for the current week
     * - standings: displays all the league standings (overall)
     * 
     * TODO: Should eventually set this up to call custom hooks.
     * 
     * @param Array $atts
     * @param String $content
     * @param String $tag
     * @return String
     */    
    public function yahoo_fantasy_shortcode($atts = [], $content = null, $tag = '') {
        
        /* @var $the_user WP_User */
       $attr_lower = array_change_key_case((array)$atts, CASE_LOWER);

       // Override default attributes with user attributes
       $options = shortcode_atts([
               'type'        => 'games',
               'seasons'     => getDate()['year'],
               'wrap'        => 'div',
               'wrap-class' => 'fantasy-sports',
               'title'       => 'Yahoo! Fantasy',
               'title-wrap'  => 'h4',
               'title-class' => 'title'
           ], $attr_lower, $tag);

       $result = '';
       $result .= "<{$options['wrap']} class=\"{$options['wrap-class']}\">";
       $result .= "<{$options['title-wrap']} class=\"{$options['title-class']}\">{$options['title']}</{$options['title-wrap']}>";

       $result .= do_shortcode($content);        
       
        switch($options['type']) {
           
            case "teams":
            case "Teams":
                require_once( __DIR__ . '/partials/YahooTeamsDisplayer.php');
                $displayer = new PublicTeamsDisplayer();
                break;
            case "leagues":
            case "Leagues":
                require_once( __DIR__ . '/partials/YahooLeaguesDisplayer.php');
                $displayer = new PublicLeaguesDisplayer();
                break;
            case "matchups":
            case "Matchups":
                break;
            case "standings":
            case "Standings":
                require_once( __DIR__ . '/partials/YahooStandingsDisplayer.php' );
                $displayer = new PublicStandingsDisplayer();
                break;
            case "games":
            case "Games":
            default:
                require_once( __DIR__ . '/partials/YahooGamesDisplayer.php' );
                $displayer = new PublicGamesDisplayer();
                break;
        } 
        
        // Get the URL from the IYahooPublicDisplayer class returned
        $url = $displayer->getRequestEndpoint($options);
        
        // Attempt the request based on the type requested and the season(s)
        // provided.  If there is an error making or getting the request, the
        // information is returned.  The error text is run through the
        // filter 'yahoo_sports_filter_request_error_text' to be replaced or appended
        if (!$this->doOAuthRequest($url)) {
            $errorText = apply_filters('yahoo_sports_filter_request_error_text',
                    "Unable to retrieve {$options['type']} data, please try a control-refresh.");
            $errorClass = apply_filters('yahoo_sports_filter_request_error_class',
                    'error');
            return $this->htmlError($errorText, $errorClass);
        }
               
        
        // Convert the 
        try {
            $fantasy = new SimpleXMLElement($this->oauth->getLastResponse());
            $result .= $displayer->getDisplayContent($fantasy);            
        } catch (Exception $ex) {
            $parseErrorText = apply_filters('yahoo_sports_filter_parse_error_text', 
                    'Could not parse Yahoo! Services response.');
            $parseErrorClass = apply_filters('yahoo_sports_filter_parse_error_class',
                    'error');                  
            $result .= $this->htmlError($parseErrorText, $parseErrorClass);
        }

        $result .= '</' . $options['wrap'] . '>';
        return $result;         
    }
    
    /**
     * Perform the request and return true or false based on whether it 
     * was successful.  The request is made based on the endpoint requested
     * and the season that is supplied.  The current year is always defaulted
     * to the season.
     * 
     * @param type $endpoint
     * @param type $season
     */
    protected function doOAuthRequest($url) {
       
        $resOk = $this->oauth->request($url);
        
        if (!$resOk){
            
            // Attempt to refresh the token
            if ($this->oauth->refreshAccessToken()) {

                // Update the Wordpress Options
                update_option('yf_access_token', $this->oauth->getAccessToken());
                update_option('yf_access_secret', $this->oauth->getAccessSecret());
                update_option('yf_access_session', $this->oauth->getAccessSession()); 
                
                // Retry the request
                //$thisOk = $this->oauth->request($url);
                
                unset($this->oauth);
                $this->oauth = new Yahoo_OAuth(get_option('yf_consumer_key'), 
                    get_option('yf_consumer_secret'), 
                    get_option('yf_access_secret'), 
                    get_option('yf_access_token'), 
                    get_option('yf_access_session'), 
                    get_option('yf_request_secret'));
                
                $resOk = $this->oauth->request($url);
            }                     
        } 
        
        return $resOk;
    }
    
    /**
     * Wrap the provided message in standard HTML.  This should be used for 
     * error messages and other single serving messages.
     * 
     * @param type $msg
     */
    private function htmlError($msg, $class = 'error') {
        return  '<span class="' . $class . '">' 
                . $msg 
                . '</span>';
    }    
   
}
