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
            controllerAs: 'keys',
            controller: function() {
                var keys = this;
                keys.consumerKey = '';
                keys.consumerSecret = '';
                keys.status = '';
                
                keys.refresh = function() {
                    console.log('Refreshing keys...');
                    
                    keys.isError = false;
                    keys.status = undefined;
                    
                    $wp.getOAuthKeys()
                            .then(function(response){
                                console.log(response);
                                var data = response.data;
                                if (!data.success) {
                                    keys.status = data.data;
                                    keys.isError = true;
                                } else {
                                    keys.consumerKey = data.data.consumerKey;
                                    keys.consumerSecret = data.data.consumerSecret;
                                }
                            }, function(failure){
                                keys.status = failure.statusText;
                                keys.isError = true;
                            });
                }
                
                keys.submit = function() {                    
                                        
                    console.log('Submitting: ' + keys.consumerKey + ':' + keys.consumerSecret);
                    
                    keys.isError = false;
                    keys.status = undefined;
                    
                    $wp.saveOAuthKeys(keys.consumerKey, keys.consumerSecret)
                            .then(function(response){
                                var data = response.data;                    
                                if (!data.success) {
                                    keys.status = data.data;
                                    keys.isError = true;
                                } else {
                                    keys.status = wp_yahoo_fantasy_plugin.text.saved_keys;
                                }                     
                            }, function(failure){
                                keys.status = failure.statusText;
                                keys.isError = true;
                            });
                };
                
                keys.cancel = function() {                           
                    keys.refresh();
                }
                
                keys.refresh();
            },            
            link: function($scope, $element, $attrs) {

            }
        };
        return ddo;
   }
   
});