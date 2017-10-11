define([
    'services/yfs.services'
], function(services){
    'use strict';
    
    services.factory('YahooOAuthFactory', [
        'WordpressFactory',
        YahooOAuthFactory
    ]);
    
    function YahooOAuthFactory($wp){        
        var oauth = {}
        oauth.config = {
            clientId: undefined,
            clientSecret: undefined,
            redirectOob: undefined                
        };

        oauth.getConfig = function(){
            return $wp
                    .get('yf_get_consumer_keys')
                    .then(function(resp){
                        console.log('YahooOAuthFactory#getConfig: ' + resp);
                        var data = resp.data;
                        if (data.success) {
                            oauth.config.clientId = data.data.consumerKey;
                            oauth.config.clientSecret = data.data.consumerSecret;
                            oauth.config.redirectOob = data.data.redirectUri === 'oob' 
                                    ? true : false;
                        }                    
                    });
        };
        
        oauth.saveConfig = function(config) {           
            return $wp
                    .post('yf_save_consumer_keys', {
                        consumerKey: config.clientId,
                        consumerSecret: config.clientSecret,
                        redirectOob: config.redirectOob
                    })
                    .then(function(resp){
                        console.log('YahooOAuthFactory#saveConfig: ' + resp);
                        oauth.config = config;
                    });
        };
        
        return oauth;
    }
});
