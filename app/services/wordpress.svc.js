define([
    'services/yfs.services'
], function(services){
    'use strict';
    
    services.factory('$wordpress', [
        '$http',
        WordpressService
    ]);
   
    function WordpressService($http) {
        return {
            getTemplate: getTemplate,
            getOAuthKeys: getOAuthKeys,
            saveOAuthKeys: saveOAuthKeys
        };        
        
        function mergeParams (params) {
            return jQuery.extend({}, {
                security: wp_yahoo_fantasy_plugin.nonce
            }, params);
        }
        
        function getTemplate(template) {
            return wp_yahoo_fantasy_plugin.base_url + '/app/directives' + template;
        }   
        
        function getOAuthKeys() {
            return $http({
               method: 'GET',
               url: wp_yahoo_fantasy_plugin.ajax_url,
               params: mergeParams({
                    action: 'get_consumer_keys'
                })
            });
        }
        
        function saveOAuthKeys(key, secret) { 
            return $http({
                method: 'POST',
                url: wp_yahoo_fantasy_plugin.ajax_url,
                headers: {
                    // Workaround for sending POST data
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                params: mergeParams({
                    action: 'save_consumer_keys'
                }),
                data: jQuery.param({
                    consumerKey: key,
                    consumerSecret: secret
                })
            });
        }             
    }
});
