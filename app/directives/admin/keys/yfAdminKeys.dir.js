define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfOauthAdmin', [
        'WordpressFactory',
        'YahooOAuthFactory',
        YahooOAuthAdmin
    ]);
   
    function YahooOAuthAdmin($wp, $oauth) {
        var ddo = {
            templateUrl: $wp.getTemplate('/admin/keys/yfAdminKeys.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: true,
            scope: {},
            bindToController: true,
            controllerAs: 'vm',
            controller: adminKeyController,            
            link: adminKeyLink
        };
        return ddo;
        
        function adminKeyLink($scope, $element, $attrs) {

        }
        
        function adminKeyController() {
            var vm = this;
            
            vm.config = $oauth.config;
            
            vm.refresh = function() {
                console.log('Refreshing OAuth Config...');
                $oauth.getConfig()
                        .then(function(){
                            vm.config = $oauth.config;
                        });
            }
            
            vm.save = function() {
                $oauth.saveConfig(vm.config)
                        .then(function(){
                            vm.config = $oauth.config;
                        });
            }          

            vm.refresh();
        }
   }
   
});