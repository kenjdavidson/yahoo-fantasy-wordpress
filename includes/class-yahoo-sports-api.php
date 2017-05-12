<?php

/**
 * The Yahoo_Sports_YQL class is a utlitiy class that provides all the requests
 * available to the plugin.
 * 
 * @since       1.1.0
 * @package     Yahoo_Fantasy
 * @subpackage  Yahoo_Fantasy/includes
 * @author      Ken Davidson <ken.j.davidson@live.ca>
 */
class Yahoo_Sports_API {
    
    /**
     * Yahoo! Sports API Base Url.  To use this, the select string is
     * appended.  
     */
    const API_BASE = 'http://fantasysports.yahooapis.com/fantasy/v2';
 
    /**
     * Yahoo! Sports API User Url.  This request returns the User container
     * data.
     */
    const USER_API_URL = 'http://fantasysports.yahooapis.com/fantasy/v2/users;use_login=1';
    
}
