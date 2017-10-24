define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfAccordion', 
            AccordionDirective);
    directives.directive('yfAccordionItem',
            AccordionItemDirective);
    
    function AccordionDirective() {
        var ddo = {
            template: '<div ng-transclude></div>',
            restrict: 'A',
            replace: true,
            transclude: true,
            scope: {},
            bindToController: true,
            controlelrAs: 'vm',
            controller: function accordionController() {
                var vm = this;
                vm.items = [];
                
                vm.addItem = function(accordionItem, initiallyOpenned){
                    vm.items.push(accordionItem);
                    accordionItem.isOpenned = initiallyOpenned ? true : false;
                };
                
                vm.closeAll = function(){
                    angular.forEach(vm.items, function(item){
                        item.isOpenned = false;
                    });
                };
                
                vm.openAll = function() {
                    angular.forEach(vm.items, function(item){
                        item.isOpenned = true;
                    });                    
                };
                
                vm.toggleItem = function(action){
                    angular.forEach(vm.items, function(item){                        
                        if (item.$id === action.$id) {
                            item.isOpenned = !item.isOpenned;
                        } else {
                            item.isOpenned = false;
                        }
                        
                    });
                };
            },
        };
        return ddo;
    }   
    
    function AccordionItemDirective() {
        var ddo = {
            template: '<div ng-transclude></div>',
            restrict: 'A',
            require: '^^yfAccordion',
            replace: false,
            transclude: true,
            scope: {
                initiallyOpen: '='
            },
            link: function accordionItemLink($scope, $element, $attrs, accordion){
                if (accordion === undefined) return;                
                
                var $titles = $('div[yf-accordion-title]', $element);
                var $contents = $('div[yf-accordion-content]', $element);
                
                $scope.isOpenned = false;
                $scope.$watch('isOpenned', function(newVal, oldVal){
                    if (newVal === oldVal) return;
                    if (newVal) {
                        $element.addClass('openned');
                    } else {
                        $element.removeClass('openned');
                    }
                    
                    $contents.slideToggle();
                });                

                $titles.on('click', function($event){            
                    accordion.toggleItem($scope);
                    $scope.$apply();
                });       
                
                accordion.addItem($scope, $scope.initiallyOpen);
                if (!$scope.initiallyOpen) $contents.slideToggle();
            }
        };
        return ddo;
    }
});