<?php

require_once __DIR__ . '/IYahooPublicDisplayer.php';

/**
 * Class used to display Game summary information.  This takes the XML response
 * from the /games collection and displays an unordered list showing the name
 * of the game, plus the year in which the game belongs.
 */
class YahooGamesDisplayer extends Yahoo_Sports_API implements iYahooPublicDisplayer {
    
    /**
     * Convert the Yahoo XML response into an unordered list of Games.  This 
     * XML should contain a <users> entry as the main level; and should be 
     * followed down to the <game> elements.
     * 
     * The output HTML is run through the 'yfs_games_output' filter, providing 
     * the output HTML and the original XML as parameters.
     * 
     * @param SimpleXMLElemnt $xml
     * @return String
     */
    public function getDisplayContent($xml, $options) {
        $output = '<ul>'; 
       
        foreach($xml->users->user[0]->games->game as $game) {
            $output .= '<li class="yahoo-fantasy yahoo-game key-' . $game->game_key . '">';
            $output .= '<span class="name">' . $game->name . '</span>';
            $output .= '<span class="season">' . $game->season . '</span>';
            $output .= '</li>';
        }            

        $output .= '</ul>';        
        return apply_filters( 'yfs_games_output', $output, $xml, $options);         
    }

    /**
     * Provides the Yahoo Sports Games endpoint.  The PublicGamesDisplay
     * class accepts and uses the following options:
     * 
     * $options = [
     *  'seasons' => String of season/year values
     * ]
     * 
     * The games API url is run through the 'yfs_games_api' filter.
     * 
     * @param Array $options
     */
    public function getRequestEndpoint($options) {
        
        $seasons = array_key_exists('seasons', $options)
                ? $options['seasons']
                : getDate()['year'];
        
        $url = Yahoo_Sports_API::API_BASE 
                . '/users;use_login=1/games;seasons='
                . $seasons;
        
        return apply_filters( 'yfs_games_api', $url, $options );
    }

}