define([
    'services/yfs.services'
], function(services){
    'use strict';
    
    services.factory('WordpressFactory', [
        '$http',
        WordpressFactory
    ]);
   
    function WordpressFactory($http) {
        return {
            get: doGet,
            post: doPost,
            getTemplate: getTemplate,
            getCurrentUserId: getCurrentUserId
        };        
        
        /**
         * Get the current user Id.  This is only applicable when on the Admin
         * pages, otherwise this will return 0.  On the regular pages, the plugin
         * updates the shortcode with the applicable User Id based on the post/
         * page.
         * @returns {wp_yahoo_fantasy_plugin.current_user}
         */
        function getCurrentUserId() {
            return wp_yahoo_fantasy_plugin.current_user
        }
        
        /**
         * Merges paramters and data.  Used as a helper method to merge
         * the parameters provided with the Wordpress Nonce
         * @param {type} params
         * @param {type} data
         * @returns {Object}
         */
        function mergeParams (params, data) {
            return jQuery.extend({}, {
                security: wp_yahoo_fantasy_plugin.nonce
            }, params, data);
        }
        
        /**
         * Requests the templates from the appropriate AJAX Admin url
         * @param {type} template
         * @returns {String}
         */
        function getTemplate(template) {
            return wp_yahoo_fantasy_plugin.base_url + '/app/directives' + template;
        }   
        
        /**
         * Performs a POST request to the $wordpress Service
         * @param string action
         * @param object data
         * @returns Promise
         */
        function doPost(action, data) {
            return $http({
                    method: 'POST',
                    url: wp_yahoo_fantasy_plugin.ajax_url,
                    headers: {
                        // Workaround for sending POST data
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    params: mergeParams({
                        action: action
                    }),
                    data: jQuery.param(data)
                }).then(function(resp){
                    return resp;
                }, function(fail){
                    console.log('Wordpress AJAX Error: ' + fail);
                });           
        }
        
        /**
         * Performs a GET request to the $wordpress Service
         * @param string action
         * @param object params
         * @returns Promise
         */
        function doGet(action, params) {
            return $http({
                    method: 'GET',
                    url: wp_yahoo_fantasy_plugin.ajax_url,
                    params: mergeParams({
                        action: action
                    }, params)
                }).then(function(resp){
                    return resp;
                }, function(fail){
                    console.log('Wordpress AJAX Error: ' + fail);
                });             
        }        
    }
});
