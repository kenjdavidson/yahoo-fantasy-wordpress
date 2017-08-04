<?php

require_once __DIR__ . '/IYahooPublicDisplayer.php';

/**
 * Formats and displays the XML provided by the Yahoo Fantasy Sports Teams
 * request.  The Teams request shows each team as a badge, displaying the
 * team logo, manager name/info, team info, standings and scoring info, etc.
 * 
 * @since       1.1.0
 * @author      Ken Davidson <ken.j.davidson@live.ca>
 */

class YahooTeamsDisplayer implements iYahooPublicDisplayer {
    
    /**
     * Converts the XML element provided by Yahoo services into HTML.
     * 
     * Output is filtered through the 'yfs_teams_output' filter, providing
     * the $output and $xml as arguments.
     * 
     * @param SimpleXMLElement $xml
     * @param Mixed $options
     * @return String html content
     */
    public function getDisplayContent($xml, $options) {
        
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
                
                $gameName = $game->name;
                $gameCode = $game->code;
                $gameSeason = $game->season;
                
                $output .= "<div class='yahoo-game'>"
                        . "<span class='game-name'>{$gameName} ({$gameSeason})</span>";
                
                foreach($game->leagues->league as $league) {
                    
                    $leagueName = $league->name;
                    $leagueScoring = $league->scoring_type;
                    
                    // There should only ever be 1 team per league, but just
                    // incase we can loop through them and display the information
                    foreach($league->teams->team as $team) {                        
                        $output .= "<div class='yahoo-team row'>"
                                . "  <div class='team-logo col-xs-4 col-sm-2 col-md-2'>"
                                . "    <img class='yahoo-logo' data-yahoo-logo='{$team->team_logos->team_logo->url}' />"
                                . "  </div>"
                                . "  <div class='team-info col-xs-8 col-sm-5 col-md-5 col-lg-5'>"
                                . "    <span class='team-name'>{$team->name}</span><br/>"
                                . "    <span class='league-name'>{$leagueName}</span></br>";
                                
                        $output .= $this->displayStandings($team, $league, $options);
                                
                        $output .= "  </div>"
                                . "  <div class='team-roster col-xs-12 col-sm-5 col-md-5'>"
                                . $this->displayRoster($team->roster)
                                . "  </div>"
                                . "</div>";   
                    }                   
                }
            
                $output .= "</div>";
            }   
            
        }           
        
        $output .= "</div>";
       
        return apply_filters( 'yfs_teams_output', $output, $xml, $options);
    }
    
    /**
     * Parse the roster information and displays the players in a list.
     * @param type $roster
     */
    private function displayRoster($roster, $options) {
        $players = '<ul>';
        
        foreach($roster->players->player as $player) {
            $players .= "<li class='player'>"
                    . "<span class='name'>{$player->name->full}</span>"
                    . "<span class='number'>{$player->uniform_number}</span>"
                    . "<span class='position'>({$player->display_position})</span>";
        }
        
        $players .= '</ul>';
        
        return $players;
    }
    
    /**
     * Parse the Standing information.  Standing information is based on either
     * the roto points, the head to head standings or text stating that the
     * league has not yet started.
     * @param XML $team
     * @param XML $league
     * @return String
     */
    private function displayStandings($team, $league, $options) {
        $standings = $team->team_standings;
        
        $outcome = ($team->league_scoring_type == "head") 
                    ? "({$standings->outcome_totals->wins} - {$standings->outcome_totals->losses} - {$standings->outcome_totals->ties})" 
                    : "";
        $points = ($team->league_scoring_type == "head")
                    ? (!$standings->points_for) ?  "" : "Points for: {$standings->points_for} Points Against: {$standings->points_against}"
                    : "Points for: {$standings->points_for} Points back: {$standings->points_back}";        
        
        if (!empty($standings->rank)) {
            return "    <span class='team-rank'>{$this->printOrdinal($standings->rank)}</span>"
                   . "    <span class='team-outcome'>{$outcome}</span><br/>"
                   . "    <span class='team-points'>{$points}</span>";    
        } else {
            return "<span>League has not started</span>";
        }        
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

    /**
     * Provides the Yahoo Sports Standings endpoint.  The PublicStandingDisplay
     * class accepts and uses the following options:
     * 
     * $options = [
     *  'seasons' => String of season/year values
     * ]
     * 
     * The API url is run through the 'yfs_teams_api' filter, with the $url
     * and $options as arguments.
     * 
     * @param type $options
     */
    public function getRequestEndpoint($options) {
        
        $seasons = array_key_exists('seasons', $options)
                ? $options['seasons']
                : getDate()['year'];
        
        //return Yahoo_Sports_API::API_BASE 
        //        . '/users;use_login=1/games;seasons=' . $seasons
        //        . '/teams;out=standings'; 
        
        $url = Yahoo_Sports_API::API_BASE
                . '/users;use_login=1/games;seasons=' . $seasons 
                . '/leagues;out=teams/teams;out=standings,roster'; 
        
        return apply_filters( 'yfs_teams_api', $url, $options );
    }

}