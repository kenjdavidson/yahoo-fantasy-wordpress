define([
    'services/yfs.services'
], function(services){
    'use strict';
    
    services.factory('YahooFantasyFactory',[
        'WordpressFactory',
        '$filter',
        YahooFantasyFactory
    ]);
    
    function YahooFantasyFactory($wp, $f) {
        
        var defaults = {
            userId: 0,
            seasons: $f('date')('yyyy')
        };
        
        var fantasy = {};
        
        function doGet(action, type, params) {
            var merged = angular.extend(defaults, params);
            return $wp.get(action, merged)
                    .then(function(resp){
                        var data = resp.data;
                        if (data.success) {
                            fantasy[type] = data.data[type];
                            return fantasy[type];
                        }
                    });
        }
        
        fantasy.getGames = function(params) {
            return doGet('yf_get_user_games', 'games', params);
        };
        
        fantasy.getLeagues = function(params) {
            return doGet('yf_get_user_leagues', 'leagues', params);
        }
        
        return fantasy;
    }
});