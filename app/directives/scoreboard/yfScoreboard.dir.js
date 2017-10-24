define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfScoreboard', [
        'DirectiveFactory',
        ScoreboardDirective
    ]);
    
    function ScoreboardDirective($df) {
        var iScope = {
            userId: '=',
            seasons: '=',
            userOnly: '='
        };
        
        return $df.build('/scoreboard/yfScoreboard.tmpl.html',
            iScope,
            scoreboardShortcodeLink,
            scoreboardShortcodeController
        );
        
        function scoreboardShortcodeLink($scope, $element, $attrs) {
            var vm = $scope.vm;
            vm.refresh();
            
            $scope.$on('shortcode.refresh.seasons', function(event, seasons){
                vm.seasons = seasons;
                vm.refresh();
            });
        }
        
        function scoreboardShortcodeController(){
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
