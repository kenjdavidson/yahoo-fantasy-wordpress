/**
 * yfs.boot.js
 * 
 * Boostrap AngularJS main module to the appropriate HTML element.
 * 
 * @author Ken Davidson
 * 
 * @see http://requirejs.org/
 * @see https://docs.angularjs.org
 */

// Initialize the RequireJS configuration
requirejs.config({
    
    // All requirejs modules loaded from here
    // wp_config is localized from Wordpress to provide the base plugin URL
    baseUrl: wp_yahoo_fantasy_plugin.base_url + '/app',
    
    // Prefix remaining modules with thes name \|P_"|||:
    paths: {
        angular: 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min',        
        ngResource: 'https://ajax.googleapis.com/ajax/libs/angularjs/X.Y.Z/angular-resource',
        controllers: 'controllers',
        directives: 'directives',
        services: 'services'
    },
    
    // Define non require javascript modules, including angular, ngResource, etc
    // which are required.
    shim: {
        'ngResource': {
            deps: ['angular'],
            exports: 'ngResource'            
        },        
        'angular': {
            exports: 'angular'
        }
    }
});

// Bootstrap the yfsApp module to the <div class="yahoo-fantasy-plugin"></div> document
// element.
require(['yfs.app'],
    function() {
        angular.bootstrap(angular.element('.yahoo-fantasy-plugin'), ['yfs.app']);
    }
);