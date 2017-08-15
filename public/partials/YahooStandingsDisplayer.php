<?php

require_once __DIR__ . '/IYahooPublicDisplayer.php';

/**
 * Class used to display Game summary information.  The standings for each 
 * league are displayed in a table showing:
 * 
 * - Team name
 * - Manager
 * - Position
 * - Points for/Wins-losses
 * 
 * First through third place are marked with solid left borders colored with
 * the matching medal (gold, silver and bronze).
 * 
 * @since       1.1.0
 * @author      Ken Davidson 
 * 
 * @uses apply_filters Filter 'yfs_standings_api' allows plugin and theme developers
 *                     to customize the Standings API url.
 * @uses apply_filters Filter 'yfs_standings_output' allows plugin and theme
 *                     developers to customize the Standings HTML output string.
 */
class YahooStandingsDisplayer implements iYahooPublicDisplayer {
    
    /**
     * Convert the Yahoo XML response into an unordered list of Games.  This 
     * XML should contain a <users> entry as the main level; and should be 
     * followed down to the <game> elements.
     * 
     * The output is run through the filter 'yfs_standings_output' using the
     * $output and $xml as arguments.
     * 
     * @param SimpleXMLElemnt $xml
     * @param Mixed $options
     * @return String
     */
    public function getDisplayContent($xml, $options) {
        
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
                    
                    $output .= $this->outputLeagueTable($league, $game, $options);

                } // End league                                                
            } // End exception
        } // End game        
        
        $output .= "</div>\n";
        
        //return $this->oauth->getLastResponse(); 
        return apply_filters( 'yfs_standings_output', $output, $xml, $options );         
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
    private function outputLeagueTable($league = null, $game = null, $options) {
        if ($league == null) return "";
        
        $gameType = $league->scoring_type;
                    
        $output = "<div class='yahoo-league {$game->name} game-{$game->game_id} league-{$league->league_id}'>\n";
        $output .= "<span class='league-name title'>{$league->name} ({$league->season})</span>\n";
        $output .= "<table>\n";
        $output .= "<thead>\n"
                . "<tr>\n"
                . "<th class='team-name'>Team Name</td>"
                . "<th class='team-manager'>Manager</td>"
                . "<th class='team-postiion'>Position</td>";

        // Base the next few columns on the specific game type and available 
        // information.
        if ($gameType == "head") {
            $output .= "<th class='team-record'>Record</td>";                      
        } else {
            $output .= "<th class='team-points-for'>Points</td>";
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

    /**
     * Provides the Yahoo Sports Standings endpoint.  The PublicStandingDisplay
     * class accepts and uses the following options:
     * 
     * $options = [
     *  'seasons' => String of season/year values
     * ]
     * 
     * API Url is pushed through filter 'yfs_standings_api' for customization.
     * the $output and $options are provided as arguments.
     * 
     * @param type $options
     */
    public function getRequestEndpoint($options) {
        
        $seasons = array_key_exists('seasons', $options)
                ? $options['seasons']
                : getDate()['year'];
        
        $url = YahooSportsAPI::API_BASE 
                . '/users;use_login=1/games;seasons=' . $seasons 
                . '/leagues;out=standings';        
        
        return apply_filters( 'yfs_standings_api', $url, $options );
    }

}