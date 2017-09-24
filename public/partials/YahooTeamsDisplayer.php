<?php

require_once __DIR__ . '/IYahooPublicDisplayer.php';

/**
 * Formats and displays the XML provided by the Yahoo Fantasy Sports Teams
 * request.  The default teams output displays:
 * 
 * - Team icon
 * - Team info: Team name, league name, standings
 * - Team roster: Players, names and positions
 * 
 * @since       1.1.0
 * @author      Ken Davidson
 * 
 * @uses apply_filters Filter 'yfs_teams_api' allows plugin and theme developers
 *                     to customize the Teams API url.
 * @uses apply_filters Filter 'yfs_teams_output' allows plugin and theme
 *                     developers to customize the Teams HTML output string.
 * @uses apply_filters Filter 'yfs_position_text' allowing the user to augment
 *                     the position (1st, 2nd, etc).
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
                
                $output .= "<div class='yahoo-game'>";
                
                foreach($game->leagues->league as $league) {
                    
                    $leagueStandings = $this->getLeagueStandings($league);
                    $currentWeek = $league->current_week;
                    
                     $output .= "<span class='league-name title'>{$league->name} (Week {$currentWeek})</span>";
                    
                    // There should only ever be 1 team per league, but just
                    // incase we can loop through them and display the information
                    foreach($league->teams->team as $team) { 
                        
                        $matchup = $team->matchups->matchup[$currentWeek-1];
                        $standings = $team->team_standings;
                        $output .= "<div class='yahoo-team matchup row'>"
                                . $this->displayTeam($matchup->teams->team[0], $leagueStandings, $options)
                                . $this->displayTeam($matchup->teams->team[1], $leagueStandings, $options)
                                .  "</div>";
                                  
                    }                   
                }
            
                $output .= "</div>";
            }   
            
        }           
        
        $output .= "</div>";
       
        return apply_filters( 'yfs_teams_output', $output, $xml, $options);
    }
    
    /**
     * Convert the leagues standings into a map of team Id to SimpleXMLElement
     * for each team.
     * @param SimpleXMLElement $league
     */
    private function getLeagueStandings($league) {
        
        foreach($league->standings->teams->team as $team) {
            $standings[(string) $team->team_key] = $team;
        }
        
        return $standings;
    }
    
    /**
     * Convert a team XML to an HTML grouping
     * @param SimpleXMLElement $team
     * @param Map<int,SimpleXMLElement> $standings
     * @param mixed $options
     * @return string
     */
    private function displayTeam($team, $standings, $options) {       
        $isPlayer = 'opponent';
        
        foreach($team->managers->manager as $manager) {
            if ($manager->is_current_login) {
                $isPlayer = 'player';
            }              
        }
        
        //$team->managers->manager[0]->is_current_login ? 'player' : 'opponent';
        $ts = $standings[(string)$team->team_key]->team_standings;
        $pos = $this->printOrdinal($ts->rank);
        
        $output = "<div class='team {$isPlayer} team-id-{$team->team_id} col-xs-12 col-sm-6 row middle-sm'>"
            . "  <div class='team-logo col-xs-2'>"
            . "    <img class='yahoo-logo' data-yahoo-logo='{$team->team_logos->team_logo->url}' />"
            . "  </div>"
            . "  <div class='team-score col-xs-3 col-sm-2'>"
            . "     <span class='total-points'>{$team->team_points->total}</span>"
            . "     <span class='projected-points'>{$team->team_projected_points->total}</span>"
            . "   </div>"
            . "   <div class='team-info col-xs-7 col-sm-8'>"
            . "     <span class='team-name'>{$team->name}</span>"
            . "     <span class='team-rank'>{$ts->outcome_totals->wins} {$ts->outcome_totals->losses} {$ts->outcome_totals->ties} {$pos}</span>"
            . "   </div>"
            . " </div>";                       
        
        return $output;
    }

    /**
     * Converts the rank into an ordinal and returns the display value.
     * @param String $rank
     * @return String ordinal
     */
    private function printOrdinal($number) {    
        $pos = '';
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        
        if (strlen($number) == 0) {
            
        } else if ((($number % 100) >= 11) && (($number%100) <= 13)) {
            $pos .= "{$number}th";
        } else {
            $pos .= $number . $ends[$number % 10];
        }
        
        return apply_filters( 'yfs_position_text', $pos);
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
        
        $url = YahooSportsAPI::API_BASE
                . '/users;use_login=1/games;seasons=' . $seasons 
                . '/leagues;out=standings,teams/teams;out=matchups';        
        
        return apply_filters( 'yfs_teams_api', $url, $options );
    }

}