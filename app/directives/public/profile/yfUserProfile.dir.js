define([
    'directives/yfs.directives'
], function(directives){
    'use strict';
    
    directives.directive('yfUserProfile',[
            'WordpressFactory',
            UserProfileDirective]);
    
    function UserProfileDirective($wp) {
        var ddo = {
            templateUrl: $wp.getTemplate('/public/profile/yfUserProfile.tmpl.html'),
            restrict: 'EA',
            replace: false,
            transclude: false,
            scope: {
                user: '='
            }
        };
        return ddo;
    }
})