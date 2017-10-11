define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfLeaguesShortcode',[
        'WordpressFactory',
        'YahooFantasyFactory',
        LeaguesShortcode
    ]);
    
    function LeaguesShortcode($wp, $yf) {
        var ddo = {
            templateUrl: $wp.getTemplate('/public/leagues/yfLeaguesShortcode.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: true,
            scope: {},
            bindToController: true,
            controllerAs: 'vm',
            controller: leaguesShortcodeController,
            link: leaguesShortcodeLink
        };
        return ddo;
        
        function leaguesShortcodeLink($scope, $element, $attrs) {
            var vm = $scope.vm;
            vm.userId = $attrs.userId;
            vm.seasons = $attrs.seasons;
            vm.refresh();
        }
        
        function leaguesShortcodeController(){
            var vm = this;
            vm.leagues = $yf.leagues;
            
            vm.refresh = function(){
                $yf.getLeagues({
                    userId: vm.userId,
                    seasons: vm.seasons
                }).then(function(){
                    vm.leagues = $yf.leagues;
                });
            };
        }
    }
});