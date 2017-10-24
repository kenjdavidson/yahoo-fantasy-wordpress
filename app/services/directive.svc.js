define([
    'services/yfs.services'
], function(services){
    'use strict';
    
    services.factory('DirectiveFactory', [
            'WordpressFactory',
            'YahooFantasyFactory',
            DirectiveFactory]);
    
    function DirectiveFactory($wp, $yf){
        return {
            build: build,
            getApiService: getApiService,
            getYahooFantasyService: getYahooFantasyService
        };
        
        function build(template, scope, ctrl, link) {
            var ddo = {
                restrict: 'EA',
                replace: false,
                transclude: true,
                scope: scope,
                bindToController: true,
                controllerAs: 'vm',
                controller: ctrl,
                link: link                
            };
            
            if (typeof template === 'function')
                ddo['template'] = template();
            else if (typeof template === 'string')
                ddo['templateUrl'] = $wp.getTemplate(template);
                
            return ddo;
        }
        
        function getApiService() {
            return $wp;
        }
        
        function getYahooFantasyService() {
            return $yf;
        }
    }
});