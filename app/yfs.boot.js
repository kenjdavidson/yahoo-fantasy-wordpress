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
    baseUrl: 'lib',
    
    // Prefix remaining modules with thes name \|P_"|||:
    paths: {
        angular: 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js',        
        ngResource: 'https://ajax.googleapis.com/ajax/libs/angularjs/X.Y.Z/angular-resource.js',
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

// Bootstrap the yfsApp module to the <div id="yfsModule"></div> document
// element.
require(['yfs.app'],
  function() {
    angular.bootstrap(angular.element('#yfsModule'), ['yfsModule']);
  }
);