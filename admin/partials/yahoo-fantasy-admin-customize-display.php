<?php

/**
 * Displays information about customization of the Yahoo Fantasy Sports
 * plugin and shortcode display.
 */

?>

<div class="yahoo-fantasy-sports">
    
    <h1>Customization Filters</h1>
    <p>
       Each type of Yahoo Fantasy Sports API call provides two filters for 
       use within plugin or theme development:
       <ul>
           <li>
               <p>
                    <b>yfs_[type]_api</b> which allows a plugin or theme to provide 
                    a customized Yahoo API URL when making a request for the specified
                    type.  The filter is called with:
                    <pre>apply_filters( 'yfs_games_api', $options );</pre>
                    where:
                    <ul>                        
                        <li><b>$options</b> are the options provided by the shortcode</li>
                    </ul>
               </p>
           </li>
           <li>
                <p>
                    <b>yfs_[type]_content</b> which allows a plugin or theme to
                    provide updates or complete customization to the output HTML
                    content prior to display.  The filter is called with:
                    <pre>apply_filters( 'yfs_games_api', $content, $xml, $options );</pre>
                    where:
                    <ul>
                        <li><b>$content</b> is the standard HTML content</li>
                        <li><b>$xml</b> is the SimpleXMLElement object returned from the API URL</li>
                        <li><b>$options</b> are the options provided by the shortcode</li>
                    </ul>
               </p>
           </li>
       </ul>
    </p>
    
    <h1>Customization Classes</h1>
    <p>
        
    </p>
</div>