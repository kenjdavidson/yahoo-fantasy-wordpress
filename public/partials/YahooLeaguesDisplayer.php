<?php

require_once __DIR__ . '/IYahooPublicDisplayer.php';

/**
 * Class used to display Leagues summary information.  The leagues displayed
 * a table per game (nfl, nhl, etc) which includes:
 * 
 * - League name
 * - Scoring type
 * - Current week
 * - Number of teams
 * - Current position
 * 
 * @since      1.1.0
 * 
 * @uses apply_filters Filter 'yfs_leagues_api' allows plugin and theme developers
 *                     to customize the Leagues API url.
 * @uses apply_filters Filter 'yfs_leaues_output' allows plugin and theme
 *                     developers to customize the Leagues HTML output string.
 */
class YahooLeaguesDisplayer implements iYahooPublicDisplayer {
    
    /**
     * Convert the Yahoo XML response into an unordered list of Games.  This 
     * XML should contain a <users> entry as the main level; and should be 
     * followed down to the <league> elements.  Games which have an
     * <exception> are skipped, as these do not have leagues.
     * 
     * The html string is passed through the filter 'yfs_leagues_output' with
     * a provided XML object that can be used to customize the HTML output
     * to display on the page.
     * 
     * @param SimpleXMLElemnt $xml
     * @return String
     */
    public function getDisplayContent($xml, $options) {
        
        $output = "<div class='yahoo-fantasy yahoo-game'>\n";
        
        foreach($xml->users->user[0]->games->game as $game) {
            
            $output .= "<div class='yahoo-game'>\n";

            if (!$game->exception) {  
                $output .= "<span class='game-name'>{$game->name} ({$game->season})</span>\n";
                $output .= $this->outputLeagueTable($game, $otions);
            }
            
            $output .= '</div>'; 
        }   
        
        $output .= "</div>\n";
        
        return apply_filters( 'yfs_leagues_output', $output, $xml, $options );      
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
    private function outputLeagueTable($game = null, $options) {
        $output = "<table>\n"
                        . "<thead><tr>\n"
                        . "<td>League Name</td>"
                        . "<td>Scoring</td>"                
                        . "<td>Week</td>"
                        . "<td>Teams</td>"
                        . "<td>Positions</td>"
                        . "</tr></thead>\n";
        
        foreach($game->leagues->league as $league) { 
            
            $scoring = ($league->scoring_type == "head") ? "Head to Head" : "Rotisserie";
            $settings = $league->settings;
            $positions = $this->getRosterPositions($settings, $options);
            
            $output .= "<tr>\n"
                . "<td class='league-name'>{$league->name}</td>\n"
                . "<td class='league-scoring'>{$scoring}</td>\n"
                . "<td class='league-week'>{$league->current_week}</td>\n"
                . "<td class='league-teams'>{$league->num_teams}</td>\n"
                . "<td class='league-positions'>{$positions}</td>\n"
                . "</tr>\n";
        }   
        
        $output .= "</table>\n";               
                
        return $output;
    }
    
    /**
     * Parse the league settings to get a comma separated list of the 
     * positions in the league.
     * @param XML $settings
     * @return String
     */
    private function getRosterPositions($settings, $options) {
        $positions = '';
        
        foreach($settings->roster_positions->roster_position as $pos) {
            $positions .= ', ' . $pos->position . '(' . $pos->count .  ')';
        }
        
        return substr($positions, 2);
    }

    /**
     * Provides the Yahoo Sports Leagues endpoint.  The PublicLeagueDisplay
     * class accepts and uses the following options:
     * 
     * $options = [
     *  'seasons' => String of season/year values
     * ]
     * 
     * The url and options are passed through the filter 'yfs_leagues_api' so 
     * that plugin/theme developers can request a new API url.
     * 
     * @param type $options
     */
    public function getRequestEndpoint($options) {
        
        $seasons = array_key_exists('seasons', $options)
                ? $options['seasons']
                : getDate()['year'];
        
        $url = YahooSportsAPI::API_BASE
                . '/users;use_login=1/games;seasons=' . $seasons 
                . '/leagues;out=settings';   
        
        return apply_filters( 'yfs_leagues_api', $url, $options);
    }

}