define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfTeamStandings',[
        '$filter',
        TeamStandingsDirective
    ]);

    function TeamStandingsDirective($filter) {
        var ddo = {
            template: '<div class="team-standings">'
                + '<span class="place">{{place}}</span>'
                + '<span class="record">{{record}}</span>'
                + '</div>',
            restrict: 'E',
            replace: true,
            transclude: false,
            scope: {
                standings: '=',
                scoringType: '='
            },
            link: teamStandingsDirectiveLink
        };
        return ddo;       
        
        function teamStandingsDirectiveLink($scope, $element, $attrs) {
            $scope.place = $scope.standings ? $filter('ordinalize')($scope.standings.rank) + " place" : undefined;
            if ("head" === $scope.scoringType) {
                var outcome = $scope.standings.outcome_totals;
                $scope.record = [outcome.wins, outcome.losses, outcome.ties].join('-');
            } else if ("roto" === $scope.scoringType) {
                $scope.record = $scope.standings.points_for + ' points (' + $scope.standings.points_back + ' back)';
            } else {
                $scope.record = undefined;  
            }
        }        
    }
});
