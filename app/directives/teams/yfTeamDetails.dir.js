define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfTeamDetails',
        TeamStandingsDirective);

    function TeamStandingsDirective() {
        var ddo = {
            template: 
                  '<div ng-if="team.league_scoring_type === \'head\'" class="team-points">'
                + '  <span ng-if="team.team_points" class="current-points">{{team.team_points.total}}</span>'
                + '  <span ng-if="team.team_projected_points" class="projected-points">{{team.team_projected_points.total}}</span>'
                + '</div>'
                + '<div class="team-image image-size-{{team.team_logos.team_logo.size}}">'
                + '  <img ng-src="{{team.team_logos.team_logo.url}}"/>'
                + '</div>'
                + '<div class="team-details">'
                + '  <span class="team-name">{{team.name}}</span>'
                + '  <yf-team-standings standings="team.team_standings" scoring-type="team.league_scoring_type"></yf-team-standings>'
                + '</div>',
            restrict: 'E',
            replace: false,
            transclude: false,
            scope: {
                team: '='
            },
            link: teamDetailsDirectiveLink
        };
        return ddo;       
    }
    
    function teamDetailsDirectiveLink($scope, $element, $attrs) {

    }    
});
