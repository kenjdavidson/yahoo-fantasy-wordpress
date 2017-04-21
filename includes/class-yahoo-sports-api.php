<?php

/**
 * The Yahoo_Sports_YQL class is a utlitiy class that provides all the requests
 * available to the plugin.
 */
class Yahoo_Sports_API {
    
    /**
     * Yahoo! Sports Query Language URL.  To use this, the select string is
     * appended.  
     */
    const API_BASE = 'http://fantasysports.yahooapis.com/fantasy/v2';
    
    /**
     * Yahoo! sports User API query String.
     */
    const API_URI = [
        'user'      => '/users;use_login=1',
        'games'     => '/users;use_login=1/games;seasons=%s',
        'leagues'   => '/users;use_login=1/games;seasons=%s/leagues',
        'teams'     => '/users;use_login=1/games;seasons=%s/teams;out=standings',
        'standings' => '/users;use_login=1/games;seasons=%s/leagues;out=standings'
    ];
    
    /**
     * Static helper method used to build the Yahoo! Sports endpoint based
     * on the request being made, and the season requested.  Based on the
     * $endpoint entered, the correct URL will be returned, after adding in
     * the provided or default season.
     * 
     * @param String $endpoint
     * @param Integer $season
     */
    public static function endpointBySeason($endpoint, $seasons = null) {
        if (!$seasons) {
            $seasons = getDate()['year'];
        }
        
        $queryString = self::API_BASE . self::API_URI[$endpoint];                 
        return sprintf($queryString, $seasons);
    }
    
}
