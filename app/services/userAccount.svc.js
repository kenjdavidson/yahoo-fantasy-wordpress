define([
    'services/yfs.services'
], function(services){
    'use strict';
    
    services.factory('YahooUserAccountFactory', [
        'WordpressFactory',
        YahooUserAccountFactory
    ]);
    
    function YahooUserAccountFactory($api){
        var user = {};
        
        user.profile = undefined;
        user.authorizationUrl = undefined;
        
        user.authorizeUser = function(code) {
            return $api.post('yf_request_auth', {authCode: code})
                    .then(function(resp){
                        console.log('YahooUserAccountFactory.authorizeUser response: ' + resp);
                        var data = response.data;
                        if (data.success) {
                            // Dont do anything currently
                        } 
                        return user;
                    });            
        };
        
        user.getProfile = function() {
            return $api.get('yf_get_user_account')
                    .then(function(resp){       
                        console.log('YahooUserAccountFactory.getProfile response: ' + resp);
                        var data = resp.data;
                        if (!data.success && data.data.errorCode === 901) {
                            user.profile = undefined;
                            user.authorizationUrl = data.data.errorMessage;
                        } else if (data.success) {
                            user.profile = data.data.account.profile;
                            user.authorizationUrl = undefined;
                        }
                        return user;
                    });
        };
        
        user.logout = function() {
            return $api.get('yf_logout')
                    .then(function(resp){
                        console.log('YahooUserAccountFactory.logout response: ' + resp);
                        var data = resp.data;
                        if (data.succcess) return true;
                        return false;
                    });
        }
        
        return user;
    }
});
