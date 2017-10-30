define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfPlayerRow',[
        'YahooFantasyFactory',
        PlayerRowDirective
    ]);
    
    function PlayerRowDirective($yf) {
        var ddo = {
            
        };
        return ddo;
    }
});