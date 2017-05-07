<?php

require_once __DIR__ . '/interface-yahoo-public-displayer.php';

/**
 * Formats and displays the XML provided by the Yahoo Fantasy Sports Teams
 * request.  The Teams request shows each team as a badge, displaying the
 * team logo, manager name/info, team info, standings and scoring info, etc.
 * 
 * @since       1.1.0
 * @author      Ken Davidson <ken.j.davidson@live.ca>
 */

class PublicTeamsDisplay implements iYahooPublicDisplayer {
    
    /**
     * Converts the XML element provided by Yahoo services into HTML.
     * 
     * @param SimpleXMLElement $xml
     */
    public function getDisplayContent($xml) {
        
        // Handle all the leagues for which the user is apart of.  For
        // whatever reason the xml returned for the games/leagues;standings
        // follows the structure league/password/weekly_deadline/renewed/standings
        $output = "<div class='yahoo-fantasy yahoo-teams'>\n";
        
        
        // Look through all the games, we actually want to take this a little 
        // further and only display team related information.
        foreach($xml->users->user[0]->games->game as $game) {           
            
            // Only work with games that are valid, if there is a game
            // <exception> then skip it.
            if (!$game->exception) {
                $output .= "<div class='yahoo-game'>"
                    . "<span class='game-name'>{$game->name} ({$game->season})</span>";
                
                foreach($game->teams->team as $team) {
                    $standings = $team->team_standings;
                    $outcome = ($team->league_scoring_type == "head") 
                                ? "({$standings->outcome_totals->wins} - {$standings->outcome_totals->losses} - {$standings->outcome_totals->ties})" 
                                : "";
                    $points = ($team->league_scoring_type == "head")
                                ? (!$standings->points_for) ?  "" : "Points for: {$standings->points_for} Points Against: {$standings->points_against}"
                                : "Points for: {$standings->points_for} Points back: {$standings->points_back}";
                    
                    $output .= "<div class='yahoo-team'>"
                            . "  <div class='team-logo'>"
                            . "    <span class='team-game'>{$game->name}</span>"
                            . "    <img class='yahoo-logo' data-yahoo-logo='{$team->team_logos->team_logo->url}' />"
                            . "  </div>"
                            . "  <div class='team-info'>"
                            . "    <span class='team-name'>{$team->name}</span>"
                            . "    <span class='team-rank'>{$this->printOrdinal($standings->rank)}</span>"
                            . "    <span class='team-outcome'>{$outcome}</span>"
                            . "    <span class='team-points'>{$points}</span>"             
                            . "  </div>"
                            . "</div>";
                }
            
                $output .= "</div>";
            }   
            
        }           
        
        $output .= "</div>";
        
        yahooSportsLogger($xml->asXML());
        return $output;
    }

    /**
     * Converts the rank into an ordinal and returns the display value.
     * @param String $rank
     * @return String ordinal
     */
    private function printOrdinal($number) {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13))
            return $number. 'th';
        else
            return $number. $ends[$number % 10];
    }
}