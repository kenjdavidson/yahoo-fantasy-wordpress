<?php

require_once __DIR__ . '/IYahooPublicDisplayer.php';

/**
 * Implements the Yahoo Matchup Displayer class.  This class provides the
 * customized endpoint and then accepts the XML and $options provided to
 * display the Matchup information as requested.   The default matchup 
 * displays team and competitor:
 * 
 * - Icon
 * - Name
 * - Current points
 * - Standings
 * - total points
 * 
 * @since 1.1.0
 * @author Kenneth Davidson
 * 
 * @uses apply_filters Filter 'yfs_matchup_api' allows plugin and theme developers
 *                     to customize the Matchup API url.
 * @uses apply_filters Filter 'yfs_matchup_output' allows plugin and theme
 *                     developers to customize the Matchup HTML output string.
 */
class YahooMatchupDisplayer implements iYahooPublicDisplayer {
    
    /**
     * Parses the XML element and creates the HTML that is used to display the
     * matchup details.
     * 
     * @param SimpleXMLElement $xml
     * @param mixed $options
     */
    public function getDisplayContent($xml, $options) {
        
    }

    /**
     * Returns the request endpoint for the Matchup API call.  Prior to returning
     * the $url is filtered using the 'yfs_matchup_api' filter call.
     * 
     * @param String $options
     * @return String
     */
    public function getRequestEndpoint($options) {
        
        $seasons = array_key_exists('seasons', $options)
                ? $options['seasons']
                : getDate()['year'];
        
        $url = YahooSportsAPI::API_BASE 
                . '/users;use_login=1/games;seasons='
                . $seasons;
        
        return apply_filters( 'yfs_matchup_api', $url, $options );        
    }

}
