define([
   'extras/yfs.extras' 
], function(extras){
    'use strict';
    
    extras.filter('ordinalize', function(){
        return function(number) {            
            if(isNaN(number) || number < 1) {                
                return number;
            } else {
                var twodigits = number % 100;
                if (twodigits >= 11 && twodigits <=13) return number + 'th';
                switch(number % 10) {
                    case 1: return number + 'st';
                    case 2: return number + 'nd';
                    case 3: return number + 'rd';
                    default: return number + 'th';
                }           
            }
        }
    });
});
