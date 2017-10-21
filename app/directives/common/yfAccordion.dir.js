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
            restrict: 'A',
            replace: false,
            transclude: false,
            link: function accordionDirectiveLink($scope, $element, $attrs){ 
                var $titles = $('div[yf-accordion-title]', $element);
                var $contents = $('div[yf-accordion-content]', $element);

                $titles.on('click', function($event){            
                    if ($element.hasClass('openned')) {
                        $element.removeClass('openned');
                    } else {
                        $element.addClass('openned');
                    }   

                    $contents.slideToggle();
                });            

                $contents.slideToggle();  
            }
        };
        return ddo;
    }   
    
    function AccordionItemDirective() {
        var ddo = {
            
        };
        return ddo;
    }
});