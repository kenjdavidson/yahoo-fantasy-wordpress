define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
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
            
            $scope.$watchGroup(['vm.selectedResource','vm.seasons'], function(newSelected){
                var shortcode = '<yf-' + newSelected[0].value + '-shortcode>';
                var $shortcode = $(shortcode)
                        .attr({
                            'seasons': vm.seasons,
                            'user-id': $wp.getCurrentUserId()
                        }).html('<h2>' + newSelected[0].label + '</h2>');
                
                $wrapper.empty();
                $wrapper.append($shortcode);
                $c($shortcode)($scope);
            });
        }
        
        function shortcodeTesterController() {
            var vm = this;
            
            vm.seasons = $f('date')(new Date(), 'yyyy');
            vm.resourceList = [{
                label: 'Games',
                value: 'games'
            },{
                label: 'Leagues',
                value: 'leagues'                    
            }, {
                label: 'Standings',
                value: 'standings'                    
            }, {
                label: 'Teams/Matchups',
                value: 'teams'                   
            }, {
                label: 'Custom',
                value: 'custom'
            }];    
        
            vm.selectedResource = vm.resourceList[0];
            
            vm.onResourceChange = function(resource){
                console.log(resource);
            }
        }
    }
});