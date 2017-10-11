define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfUserAccount', [
        'WordpressFactory',
        'YahooUserAccountFactory',
        YahooFantasyUserAccount
    ]);
    
    function YahooFantasyUserAccount($wp, $user) {
        var ddo = {
            templateUrl: $wp.getTemplate('/admin/login/yfUserAccount.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: true,
            scope: {},
            bindToController: true,
            controllerAs: 'vm',
            controller: userAccountController,
            link: userAccountLink
        };
        return ddo;
        
        function userAccountLink($scope, $element, $attrs) {
            var vm = $scope.vm;
        }
        
        function userAccountController() {
            var vm = this;

            vm.profile = $user.profile;
            vm.authorizationUrl = $user.authorizationUrl;
            
            vm.refresh = function() {
                console.log('Refreshing user account...');                
                $user.getProfile()
                        .then(function(){
                            vm.profile = $user.profile;
                            vm.authorizationUrl = $user.authorizationUrl;
                        });
            }

            vm.submit = function() {
                console.log('Submitting: Authorization Code ' + vm.authorizationCode);
                $user.authorizeUser(vm.authorizationCode)
                        .then(vm.refresh);
            };

            vm.cancel = function() {
                vm.authorizationCode = undefined;
                vm.refresh();
            };

            vm.logout = function() {
                console.log('Logout Requested...');
                $user.logout()
                        .then(vm.refresh);
            };

            vm.refresh();
        }
    }
})
