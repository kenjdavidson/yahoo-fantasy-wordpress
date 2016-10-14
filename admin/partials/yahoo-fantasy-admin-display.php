<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Yahoo_Fantasy
 * @subpackage Yahoo_Fantasy/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
<h1>Yahoo Fantasy Settings</h1>
<form method="post" action="options.php">
<?php settings_fields( 'yfoption-group' ); ?>
<?php do_settings_sections( 'yfoption-group' ); ?>
    <div class="option-field">Yahoo Fantasy Consumer Key:<br>
    <input type="text" name="yf_consumer_key" value="<?php echo esc_attr( get_option('yf_consumer_key') ); ?>" />
    </div>
    <div class="option-field">Yahoo Fantasy Consumer Secret:<br>
    <input type="text" name="yf_consumer_secret" value="<?php echo esc_attr( get_option('yf_consumer_secret') ); ?>" />
    </div>
    <div class="option-field">Yahoo Fantasy API URL:<br>
    <input type="text" name="yf_url" value="<?php echo esc_attr( get_option('yf_url') ); ?>" />
    </div>
<?php submit_button(); ?>
</form>
</div>