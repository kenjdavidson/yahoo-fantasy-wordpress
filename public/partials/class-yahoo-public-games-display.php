<?php

require_once __DIR__ . '/interface-yahoo-public-displayer.php';

/**
 * Class used to display Game summary information.  This takes the XML response
 * from the /games collection and displays an unordered list showing the name
 * of the game, plus the year in which the game belongs.
 */
class PublicGamesDisplay implements iYahooPublicDisplayer {
    
    /**
     * Convert the Yahoo XML response into an unordered list of Games.  This 
     * XML should contain a <users> entry as the main level; and should be 
     * followed down to the <game> elements.
     * 
     * @param SimpleXMLElemnt $xml
     * @return String
     */
    public function display($xml) {
        $output = '<ul>'; 
       
        foreach($xml->users->user[0]->games->game as $game) {
            $output .= '<li class="yahoo-fantasy yahoo-game key-' . $game->game_key . '">';
            $output .= '<span class="name">' . $game->name . '</span>';
            $output .= '<span class="season">' . $game->season . '</span>';
            $output .= '</li>';
        }            

        $output .= '</ul>';        
        return $output;         
    }

}