<?php
namespace YahooFantasySports;

/**
 * YahooFantasyShortcodeHandler
 * 
 * Associate and handle the Shortcodes.  A number of short codes are available
 * that will display a specific set of Angular elements:
 * 
 * <yahoo-fantasy-leagues>
 * <yahoo-fantasy-teams>
 * <yahoo-fantasy-standings>
 * <yahoo-fantasy-rosters>
 * 
 * @author Kenneth Davidson
 * @version 2.0.0
 */
class YahooFantasyShortcodeHandler {
    
    protected $plugin;
    
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
    public function __construct($plugin) {             
        $this->plugin = $plugin;
        $this->init();
    }    
    
    /**
     * Initialize the object
     */
    private function init() {
        add_shortcode('yahoofantasysports', array(&$this, 'handleShortcode'));
    }
    
    /**
     * Handle the shortcode request.  Short codes are based on the type attribute
     * passing all $atts through to the angular directive.
     * [yahoo-fantasy type="team-matchups" seasons="2017"/] would be converted
     * to <yf-team-matchups seasons="2017/>.   At this point I see no reason
     * for error handling, as there is a short code tester on the admin page,
     * therefore the HTML element just wont be angularized.
     * 
     * @param Array $atts
     * @param String $content
     * @param String $tag
     */
    public function handleShortcode($atts = [], $content = null, $tag = '') {
        /* @var $the_user WP_User */
        $attr_lower = array_change_key_case((array)$atts, CASE_LOWER);
       
        // Override default attributes with user attributes
        $options = shortcode_atts([
                'wrap'        => 'p',
                'wrap-class' => '',            
                'seasons'     => getDate()['year'],
                'class'     => 'yahoo-fantasy-shortcode'                            
            ], $attr_lower, $tag);

        $authorId = get_the_author_meta( 'ID' );
        
        $sc = '';        
        if ($options['wrap']) {
            $sc .= "<{$options['wrap']} class=\"yahoo-fantasy-plugin {$options['wrap-class']}\">";
        }
        
        $sc .= "<yf-{$attr_lower['type']} seasons=\"{$options['seasons']}\" user-id=\"{$authorId}\" class=\"{$options['class']}\">";
        $sc .= "</yf-{$attr_lower['type']}>";
        
        if ($options['wrap']) {
            $sc .= "</{$options['wrap']}>";
        }
        return $sc;
    }
}