<?php

require_once __DIR__ . '/interface-yahoo-public-displayer.php';

/**
 * Class used to display Game summary information.  This takes the XML response
 * from the /games collection and displays an unordered list showing the name
 * of the game, plus the year in which the game belongs.
 */
class PublicStandingsDisplay implements iYahooPublicDisplayer {
    
    /**
     * Convert the Yahoo XML response into an unordered list of Games.  This 
     * XML should contain a <users> entry as the main level; and should be 
     * followed down to the <game> elements.
     * 
     * @param SimpleXMLElemnt $xml
     * @return String
     */
    public function display($xml) {
        
        // Handle all the leagues for which the user is apart of.  For
        // whatever reason the xml returned for the games/leagues;standings
        // follows the structure league/password/weekly_deadline/renewed/standings
        $output = "<div class='yahoo-fantasy yahoo-leagues-standings'>\n";
        
        // Look through all the games returned
        foreach($xml->users->user[0]->games->game as $game) {
            
            // Only work with games that are valid, if there is a game
            // <exception> then skip it.
            if (!$game->exception) {               
                
                foreach($game->leagues->league as $league) {
                    
                    $output .= $this->outputLeagueTable($league, $game);

                } // End league                                                
            } // End exception
        } // End game        
        
        $output .= "</div>\n";
        
        //return $this->oauth->getLastResponse(); 
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
    private function outputLeagueTable($league = null, $game = null) {
        if ($league == null) return "";
        
        $gameType = $league->scoring_type;
                    
        $output = "<div class='yahoo-league {$game->name} game-{$game->game_id} league-{$league->league_id}'>\n";
        $output .= "<span class='league-name'>{$league->name} ({$league->season})</span>\n";
        $output .= "<table>\n";
        $output .= "<thead>\n"
                . "<tr>\n"
                . "<td class='team-name'>Team Name</td>"
                . "<td class='team-manager'>Manager</td>"
                . "<td class='team-postiion'>Position</td>";

        // Base the next few columns on the specific game type and available 
        // information.
        if ($gameType == "head") {
            $output .= "<td class='team-record'>Record</td>";                      
        } else {
            $output .= "<td class='team-points-for'>Points</td>";
        }

        $output .= "</tr>\n"
                . "</thead>\n";

        foreach($league->standings->teams->team as $team) {
            
            $myTeamClass = $team->is_owned_by_current_login ? "users-team" : "";
            
            $output .= "<tr class='team key-{$team->team_key} position-{$team->team_standings->rank} {$myTeamClass}'>\n";                        
            $output .= "<td class='team-name'>{$team->name}</td>\n";
            $output .= "<td class='team-manager'>"
                    . "<img class='team-image' data-imagesrc='{$team->team_logos->team_logo->url}'></img>"
                    . "{$team->managers->manager->nickname}</td>\n"
                    . "<td class='team-position'>{$team->team_standings->rank}</td>\n";                    

            if ($gameType == "head") {
                $output .= "<td class='team-record'>"
                        . "{$team->team_standings->outcome_totals->wins}"
                        . " - {$team->team_standings->outcome_totals->losses}"
                        . " - {$team->team_standings->outcome_totals->ties}"
                        . "</td>\n";                      
            } else {
                $output .= "<td class='team-points-for'>{$team->team_standings->points_for}</td>\n";
            }                    

            $output .= "</tr>\n";
        } // End team                        

        $output .= "</table>\n";
        $output .= "</div>\n";
                
        return $output;
    }    
}