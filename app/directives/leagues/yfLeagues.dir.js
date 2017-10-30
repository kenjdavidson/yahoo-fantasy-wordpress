define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfLeagues',[
        'WordpressFactory',
        'YahooFantasyFactory',
        LeaguesShortcode
    ]);
    
    function LeaguesShortcode($wp, $yf) {
        var ddo = {
            templateUrl: $wp.getTemplate('/leagues/yfLeagues.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: true,
            scope: {
                userId: '=',
                seasons: '='
            },
            bindToController: true,
            controllerAs: 'vm',
            controller: leaguesShortcodeController,
            link: leaguesShortcodeLink
        };
        return ddo;
        
        function leaguesShortcodeLink($scope, $element, $attrs) {
            var vm = $scope.vm;
            vm.refresh();
            
            $scope.$on('shortcode.refresh.seasons', function(event, seasons){
                vm.seasons = seasons;
                vm.refresh();
            });
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