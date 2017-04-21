<?php

require_once __DIR__ . '/interface-yahoo-public-displayer.php';

/**
 * Formats and displays the XML provided by the Yahoo Fantasy Sports Teams
 * request.  The Teams request shows each team as a badge, displaying the
 * team logo, manager name/info, team info, standings and scoring info, etc.
 * 
 * @since       1.1.0
 * @author      Ken Davidson <ken.j.davidson@live.ca>
 */

class PublicTeamsDisplay implements iYahooPublicDisplayer {
    
    /**
     * Converts the XML element provided by Yahoo services into HTML.
     * 
     * @param SimpleXMLElement $xml
     */
    public function display($xml) {
        return $xml->asXML();
    }

}