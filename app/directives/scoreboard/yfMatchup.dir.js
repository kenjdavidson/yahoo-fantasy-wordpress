define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfMatchup',[
        '$compile',
        'DirectiveFactory',
        MatchupDirective
    ]);
    
    function MatchupDirective($c, $df) {
        var iScope = {
            matchup: '='
        };
        
        var ddo = $df.build(undefined,
            iScope,
            matchupDirectiveCtrl,
            matchupDirectiveLink
        );
        
        return ddo;
        
        function matchupDirectiveLink($scope, $element, $attrs) {
            $element.html(getTemplate($scope.vm.matchup));
            $c($element.contents())($scope);
        }            
        
        function matchupDirectiveCtrl() {
            
        }
    }
    
    function getTemplate(matchup) {
        if (matchup.scoring_type === 'roto') {
            return ROTO_TEMPLATE;
        } else if (matchup.status === 'postevent') {
            return FINISHED_TEMPLATE;
        }
        
        return MATCHUP_TEMPLATE;
    }
    
    var MATCHUP_TEMPLATE = 
              '<div class="team-matchups matchups-{{vm.userId}}">'
            + '  <yf-team-details team="vm.matchup.teams[0]" class="home-team team-{{vm.matchup.teams[0].team_key}}"></yf-team-details>'
            + '  <yf-team-details team="vm.matchup.teams[1]" class="away-team team-{{vm.matchup.teams[1].team_key}}"></yf-team-details>'
            + '</div>';
    
    var FINISHED_TEMPLATE = 
              '';
      
    var ROTO_TEMPLATE = 
              '<div class="team-matchups matchups-{{vm.userId}}">'
            + '  <yf-team-details ng-repeat="team in vm.matchup.standings | filter:{is_owned_by_current_login:1}" team="team" class="roto team-{{vm.matchup.teams[0].team_key}}"></yf-team-details>'
            + '</div>';
});