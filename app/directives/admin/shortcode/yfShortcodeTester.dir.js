define([
    'jquery',
    'directives/yfs.directives'
], function($, directives){
    'use strict';
    
    const RESOURCE_LIST = [{
                label: 'Games',
                value: 'games'
            },{
                label: 'Leagues',
                value: 'leagues'                    
            }, {
                label: 'Standings',
                value: 'standings'                    
            }, {
                label: 'Matchups',
                value: 'matchups'                    
            }, {
                label: 'Teams (w/ Rosters)',
                value: 'teams'                   
            }];
    
    directives.directive('yfShortcodeTester',[
        'WordpressFactory',
        '$filter',
        '$compile',
        ShortcodeTester]);
    
    function ShortcodeTester($wp, $f, $c) {       
        var ddo = {
            templateUrl: $wp.getTemplate('/admin/shortcode/yfShortcodeTester.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: false,
            scope: {},
            bindToController: true,
            controllerAs: 'vm',
            controller: shortcodeTesterController,
            link: shortcodeTesterLink            
        };
        return ddo;    
        
        function shortcodeTesterLink($scope, $element, $attrs) {
            var vm = $scope.vm;
            var $wrapper = $('#shortcode-container', $element);
            var dirScope = undefined;
            
            $scope.$watch('vm.selectedResource', function(newSelected){
                $wrapper.empty();
                
                if (dirScope !== undefined) {
                    dirScope.$destroy();
                }                
                
                // Must be after to allow for compilation
                vm.shortcode = $wp.buildShortcode(newSelected.value, {
                    'wrap': 'div',
                    'wrap-class': '',
                    'seasons': vm.seasons
                });
                
                var shortcode = '<yf-' + newSelected.value + '>';
                var $shortcode = $(shortcode)
                        .attr({
                            'seasons': vm.seasons,
                            'user-id': $wp.getCurrentUserId()
                        }).html('<h2>' + newSelected.label + '</h2>');                                                            
                
                dirScope = $scope.$new(false, $scope);
                $c($shortcode)(dirScope, function($ce, $s){
                    $wrapper.append($ce);
                });     
                
            });
            
            $scope.$watch('vm.seasons', function(newVal){
                $scope.$broadcast('shortcode.refresh.seasons', vm.seasons);
            });
        }
        
        function shortcodeTesterController() {
            var vm = this;
            
            vm.seasons = $f('date')(new Date(), 'yyyy');
            vm.resourceList = RESOURCE_LIST;    
        
            vm.selectedResource = vm.resourceList[0];
            
            vm.onResourceChange = function(resource){
                console.log(resource);
            };
        }
    }
});