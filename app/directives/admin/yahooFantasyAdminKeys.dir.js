define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yahooFantasyAdminKeys', [
        '$wordpress',
        YahooFantasyAdminKeys
    ]);
   
    function YahooFantasyAdminKeys($wp) {
        var ddo = {
            templateUrl: $wp.getTemplate('/admin/yahooFantasyAdminKeys.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: true,
            scope: {},
            bindToController: true,
            controller: function() {
                var keys = this;
                keys.consumerKey = '';
                keys.consumerSecret = '';
                keys.status = '';
                
                keys.submit = function() {
                    console.log('Submitting: ' + keys.consumerKey + ':' + keys.consumerSecret);
                    $wp.doAjax('GET', {
                        action: 'get_oauth_keys',
                        consumerKey: keys.consumerKey,
                        consumerSecret: keys.consumerSecret
                    }, function(success){}, function(failure){});
                };
                
                keys.clear = function() {
                    console.log('Cleared');
                    keys.consumerKey = undefined;
                    keys.consumerSecret = undefined;
                }
            },
            controllerAs: 'keys',
            bindToController: true,
            link: function($scope, $element, $attrs) {

            }
        };
        return ddo;
   }
});