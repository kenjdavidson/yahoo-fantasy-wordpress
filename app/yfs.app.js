/**
 * yfs.app.js
 * 
 * Yahoo Fantasy Sports plugin application file.  Responsible for configuring
 * the AngularJS application module; as well as configuring the RequireJS
 * module locations.
 * 
 * Defines the yfsModule requirements and function.  The yfsModule requires
 * services, controllers, directives, etc.  These AngularJS modules will be
 * loaded prior to the yfsModule creation.  The available directives (and
 * controllers) available in this module are:
 * 
 * <yahoo-fantasy-games>
 * <yahoo-fantasy-leagues>
 * <yahoo-fantasy-standings>
 * <yahoo-fantasy-teams>
 * 
 * Each of which accepts the following attributes:
 * 
 * season="@Array" comma separated list of years
 * key="@String" the key for a specific Yahoo Object
 * 
 * @author Ken Davidson
 * 
 * @see http://requirejs.org/
 * @see https://docs.angularjs.org
 */

define([
    'angular',
    'services/includes',
    'directives/includes'
], function(angular){
   'use strict';
   
   return angular.module('yfs.app', [
       'yfs.services',
       'yfs.directives'
   ]);
});