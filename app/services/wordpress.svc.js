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
            doAjax: doAjax
        };        
        
        function getTemplate(template) {
            return wp_yahoo_fantasy_plugin.base_url + '/app/directives' + template;
        };        
        
        function doAjax(method, params, yes, no) {
            $http({
                url: wp_yahoo_fantasy_plugin.ajax_url,
                method: method,
                params: params
            }).then(function(success){
                console.log(success.status + ' -> ' + success.statusText);
                if (yes !== undefined) yes(success);
            }, function(failure){
                console.log(failure.status + ' -> ' + failure.statusText);
                if (no !== undefined) no(failure);
            });
        }
    }
});
