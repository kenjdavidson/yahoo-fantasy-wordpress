define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfMatchups', [
        '$filter',
        'DirectiveFactory',
        TeamMatchupsDirective
    ]);
    
    function TeamMatchupsDirective($filter, $df) {
        var iScope = {
            userId: '=',
            seasons: '='
        };
        
        var ddo = $df.build('/scoreboard/yfMatchups.tmpl.html',
            iScope,
            teamMatchupsShortcodeController,
            teamMatchupsShortcodeLink
        );

        return ddo;
        
        function teamMatchupsShortcodeLink($scope, $element, $attrs) {
            var vm = $scope.vm;
            
            if (!vm.leagues) vm.refresh();
            
            $scope.$on('shortcode.refresh.seasons', function(event, seasons){
                vm.seasons = seasons;
                vm.refresh();
            });
        }
        
        function teamMatchupsShortcodeController(){
            var vm = this;
            vm.leagues = $df.getYahooFantasyService().leagues;
            
            vm.refresh = function(){
                $df.getYahooFantasyService().getLeagues({
                    userId: vm.userId,
                    seasons: vm.seasons
                }).then(function(){
                    vm.leagues = $df.getYahooFantasyService().leagues;
                });
            };
            
            vm.getUserScoreboard = function(league) {
                if (league.scoring_type === 'roto') return league;
                for(var i = 0; i < league.scoreboard.length; i++){                    
                    if (league.scoreboard[i].teams[0].is_owned_by_current_login === "1"
                            || league.scoreboard[i].teams[1].is_owned_by_current_login === "1") {
                        return league.scoreboard[i];
                    }
                }
            };
            
            vm.getCurrentWeek = function(league){
                if (league.scoring_type === 'roto') {
                    return '(' + league.scoring_type + ')';
                } else if (league.is_finished) {                    
                    for (var i = 0; i < league.standings.length; i++) {
                        if (league.standings[i].is_owned_by_current_login === "1") {
                            return '(Finished ' + $filter('ordinalize')(league.standings[i].team_standings.rank) + ' overall)';
                        }
                    }
                } 
                
                return '(Week ' + league.current_week + ')';
            }
        }        
    }
});
