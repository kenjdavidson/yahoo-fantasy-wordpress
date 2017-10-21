define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfGames',[
        'WordpressFactory',
        'YahooFantasyFactory',
        GamesShortcodeDirective
    ]);
    
    function GamesShortcodeDirective($wp, $yf) {
        var ddo = {
            templateUrl: $wp.getTemplate('/games/yfGames.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: true,
            scope: {
                userId: '=',
                seasons: '='
            },
            bindToController: true,
            controllerAs: 'vm',
            controller: gamesShortcodeController,
            link: gamesShortcodeLink
        };
        return ddo;
        
        function gamesShortcodeLink($scope, $element, $attrs) {
            var vm = $scope.vm;    
            vm.refresh();
            
            $scope.$on('shortcode.refresh.seasons', function(event, seasons){
                vm.seasons = seasons;
                vm.refresh();
            });          
        }

        function gamesShortcodeController(){
            var vm = this;
            vm.games = $yf.games;
            
            vm.refresh = function(){
                $yf.getGames({
                        userId: vm.userId,
                        seasons: vm.seasons
                    }).then(function(){
                        vm.games = $yf.games;
                    });
            };
        }        
    }    
});
