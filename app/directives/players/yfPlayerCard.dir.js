define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfPlayerCard',[
        'YahooFantasyFactory',
        PlayerCardDirective
    ]);
    
    function PlayerCardDirective($yf) {
        var ddo = {
            template: 
                  '<div class="yf-player-card id-{{player.player_key}}">'                
                + '  <div class="player-image image-size-{{player.headshot.size}}">'
                + '    <img ng-src="{{player.headshot.url}}" />'
                + '  </div>'
                + '  <div class="player-info">'
                + '     <span class="player-name">{{player.name.full}} (#{{player.uniform_number}})</span>'
                + '     <span class="player-position">{{player.display_position}}</span>'
                + '     <span class="player-team-name">{{player.editorial_team_full_name}}</span>'        
                + '  </div>'
                + '</div>',
            restrict: 'E',
            replace: true,
            transclude: false,
            scope: {
                player: '='
            }
        };
        return ddo;
    }
});