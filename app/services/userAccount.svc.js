define([
    'services/yfs.services'
], function(services){
    'use strict';
    
    services.factory('YahooUserAccountFactory', [
        'WordpressFactory',
        YahooUserAccountFactory
    ]);
    
    function YahooUserAccountFactory($wp){
        var user = {};
        
        user.profile = undefined;
        user.authorizationUrl = undefined;
        
        user.authorizeUser = function(code) {
            return $wp.post('yf_request_auth', {authCode: code})
                    .then(function(resp){
                        console.log('YahooUserAccountFactory#authorizeUser' + resp);
                        var data = response.data;
                        if (data.success) {

                        } 
                    });            
        };
        
        user.getProfile = function() {
            return $wp.get('yf_get_user_account')
                    .then(function(resp){                        
                        var data = resp.data;
                        if (!data.success && data.data.errorCode === 901) {
                            user.profile = undefined;
                            user.authorizationUrl = data.data.errorMessage;
                        } else if (data.success) {
                            user.profile = data.data.account.profile;
                            user.authorizationUrl = undefined;
                        }
                    });
        };
        
        user.logout = function() {
            return $wp.get('yf_logout')
                    .then(function(resp){
                        var data = resp.data;
                        if (data.succcess) return true;
                        return false;
                    });
        }
        
        return user;
    }
});
