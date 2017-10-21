define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfStandings', [
       'WordpressFactory',
       'YahooFantasyFactory',
       StandingsShortcodeDirective
    ]);
    
    function StandingsShortcodeDirective($wp, $yf) {
        var ddo = {
            templateUrl: $wp.getTemplate('/standings/yfStandings.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: true,
            scope: {
                userId: '=',
                seasons: '=',
                leagueKey: '='
            },
            bindToController: true,
            controllerAs: 'vm',
            controller: standingsShortcodeController,
            link: standingsShortcodeLink
        };
        return ddo;
        
        function standingsShortcodeLink($scope, $element, $attrs) {
            var vm = $scope.vm;
            vm.refresh();
            
            $scope.$on('shortcode.refresh.seasons', function(event, seasons){
                vm.seasons = seasons;
                vm.refresh();
            });                          
        }
        
        function standingsShortcodeController() {
            var vm = this;
            vm.leagues = $yf.leagues;
            
            vm.refresh = function(){
                $yf.getLeagues({
                    userId: vm.userid,
                    seasons: vm.seasons,
                    leagueKey: vm.leagueKey
                }).then(function(){
                   vm.leagues = $yf.leagues; 
                });
            }
            
            vm.getOutcomeTotals = function(outcome) {
                if (outcome === undefined) return undefined;
                return outcome.wins + "-" + outcome.losses + "-" + outcome.ties;
            }
            
            vm.getStreak = function(streak) {
                if (streak === undefined) return undefined;
                return streak.type.substr(0,1).toUpperCase() + '-' + streak.value;
            }
            
            vm.getStandingsTemplate = function(league){
                if ('head' === league.scoring_type
                        && undefined !== league.standings[0].division_id) {
                    return $wp.getTemplate('/standings/yfStandingsHeadDiv.tmpl.html');
                } else if ('head' === league.scoring_type) {
                    return $wp.getTemplate('/standings/yfStandingsHead.tmpl.html');
                } else if ('roto' === league.scoring_type) {
                    return $wp.getTemplate('/standings/yfStandingsRoto.tmpl.html');
                } else {
                    return $wp.getTemplate('/standings/yfStandingsUnknown.tmpl.html');
                }
            }            
        }
    }
})