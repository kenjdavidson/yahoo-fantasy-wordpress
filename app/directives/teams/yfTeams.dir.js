define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfTeams',[
        'WordpressFactory',
        'YahooFantasyFactory',
        TeamsShortcodeDirective
    ]);
    
    function TeamsShortcodeDirective($wp, $yf) {
        var ddo = {
            templateUrl: $wp.getTemplate('/teams/yfTeams.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: true,
            scope: {
                userId: '=',
                seasons: '=',
                teamKey: '='
            },
            bindToController: true,
            controllerAs: 'vm',
            controller: teamsShortcodeController,
            link: teamsShortcodeLink            
        };
        return ddo;
        
        function teamsShortcodeLink($scope, $element, $attrs){
            var vm = $scope.vm;
            vm.refresh();
            
            $scope.$on('shortcode.refresh.seasons', function(event, seasons){
                vm.seasons = seasons;
                vm.refresh();
            });            
        }
        
        function teamsShortcodeController(){
            var vm = this;
            vm.teams = $yf.teams;
            
            vm.refresh = function(){
                $yf.getTeams({
                    userId: vm.userId,
                    seasons: vm.seasons,
                    teamKey: vm.teamKey
                }).then(function(){
                   vm.teams = $yf.teams; 
                });
            }            
        }
    }
});