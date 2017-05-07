<?php

require_once __DIR__ . '/interface-yahoo-public-displayer.php';

/**
 * Class used to display Leagues summary information.  This takes the XML response
 * from the /leagues collection and displays an unordered list showing the name
 * of the game, plus the year in which the game belongs.
 * 
 * @since      1.1.0
 * @author     Ken Davidson <ken.j.davidson@live.ca>
 */
class PublicLeaguesDisplay implements iYahooPublicDisplayer {
    
    /**
     * Convert the Yahoo XML response into an unordered list of Games.  This 
     * XML should contain a <users> entry as the main level; and should be 
     * followed down to the <league> elements.  Games which have an
     * <exception> are skipped, as these do not have leagues.
     * 
     * @param SimpleXMLElemnt $xml
     * @return String
     */
    public function getDisplayContent($xml) {
        
        $output = "<div class='yahoo-fantasy yahoo-game'>\n";
        
        foreach($xml->users->user[0]->games->game as $game) {
            
            $output .= "<div class='yahoo-game'>\n";

            if (!$game->exception) {  
                $output .= "<span class='game-name'>{$game->name} ({$game->season})</span>\n";
                $output .= $this->outputLeagueTable($game);
            }
            
            $output .= '</div>'; 
        }   
        
        $output .= "</div>\n";
        
        return $output;       
    }
                    
   /**
     * Creates the Yahoo! League table displaying names, managers, standings
     * points and more.  This is used for all sports, based on head  
     * or roto.  Game type Head has either: points for/against or wins/losses;
     * roto games have only points for as a total.
     * 
     * @param XML $league
     * @return String
     */
    private function outputLeagueTable($game = null) {
        $output = "<table>\n"
                        . "<thead><tr>\n"
                        . "<td>League Name</td>"
                        . "<td>Start Date</td>"
                        . "<td>End Date</td>"
                        . "<td>Scoring Type</td>"
                        . "<td>Teams</td>"
                        . "</tr></thead>\n";
        
        foreach($game->leagues->league as $league) { 
            
            $scoring = ($league->scoring_type == "head") ? "Head to Head" : "Rotisserie";
            
            $output .= "<tr>\n"
                . "<td class='league-name'>{$league->name}</td>\n"
                . "<td class='league-start'>{$league->start_date}</td>\n"
                . "<td class='league-end'>{$league->end_date}</td>\n"
                . "<td class='league-scoring'>{$scoring}</td>\n"
                . "<td class='league-teams'>{$league->num_teams}</td>\n"
                . "</tr>\n";
        }   
        
        $output .= "</table>\n";               
                
        return $output;
    }                    
}