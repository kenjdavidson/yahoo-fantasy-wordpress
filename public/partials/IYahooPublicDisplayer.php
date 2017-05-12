<?php

/**
 * Interface used to provide display functionality for different types of
 * requests.  Each class implementing this interface will define it its
 * display() method, which returns the HTML output that will be displayed
 * for this specific request
 */
interface iYahooPublicDisplayer {
    
    /**
     * Display function is used to convert Yahoo Sports XML response into
     * appropriate XML.  XML should follow the standard rules where:
     * 
     * 1) <div> wraps all the major elements: game, league, team
     * 2) <span> wraps all the text information
     * 3) <img> should include the attribute data-loadimg="" to lazy load
     * 
     * @param SimpleXMLElement $xml
     * @return String HTML content to display
     */
    public function getDisplayContent($xml);
    
    /**
     * Returns the requested endpoint specified by this displayer Class.  The
     * provided $options can be used in any way the the Class requires.  
     * 
     * @param Array $options 
     * @return String the request URL
     */
    public function getRequestEndpoint($options);
    
}