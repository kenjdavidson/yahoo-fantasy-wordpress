<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/public/partials
 */

    echo '<h2>XML Object:</h2>';
    var_dump($yf_content);

    $yf_contentA = json_decode(json_encode($yf_content), TRUE);
    echo '<br><h2>As Array:</h2>';
    print_r($yf_contentA)
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
