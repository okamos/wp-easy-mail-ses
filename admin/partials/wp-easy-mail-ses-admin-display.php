<?php

/**
 * Provide a admin area view for the plugin.
 *
 * @package WpEasyMailSES
 * @author  Shinichi Okamoto <himinato.k@gmail.com>
 * @license GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.html
 * @link    https://github.com/okamos/wp-easy-mail-ses
 */

$id = 'wp-easy-mail-ses';
?>
<div class="wrap">
    <h1><?php _e('WP easy mail SES', $id); ?></h1>
    <form name="form" method="post" action="">
        <?php wp_nonce_field($id, 'verify_email'); ?>
        <h2><?php _e('AWS settings', $id) ?></h2>
        <div class="field">
            <label>
                <span><?php _e('AWS access key id', $id); ?></span>
                <input type="text" name="access_key" autofocus value="<?php echo esc_attr($opts['access_key']); ?>">
            <label>
        </div>

        <div class="field">
            <label>
                <span><?php _e('AWS secret key ID', $id); ?></span>
                <input type="password" name="secret_key" value="<?php echo esc_attr($opts['secret_key']); ?>">
            </label>
        </div>

        <div class="field">
            <label>
                <span><?php _e('AWS region', $id); ?></span>
                <select name="region">
                    <option value="us-east-1" <?php
                    if ($opts['region'] == 'us-east-1') {
                        echo 'selected';
                    } ?>>
                        <?php echo _e('United States east 1', $id); ?>
                    </option>
                    <option value="us-west-2" <?php
                    if ($opts['region'] == 'us-west-2') {
                        echo 'selected';
                    } ?>>
                        <?php echo _e('United States west 2', $id); ?>
                    </option>
                    <option value="eu-west-1" <?php
                    if ($opts['region'] == 'eu-west-1') {
                        echo 'selected';
                    } ?>>
                        <?php echo _e('European Union west 1', $id); ?>
                    </option>
                </select>
            </label>
        </div>

        <h2><?php _e('Sender settings', $id); ?></h2>
        <div class="field">
            <label>
                <span><?php _e('Sender Email address', $id); ?></span>
                <input type="email" name="from_email" value="<?php echo esc_attr($opts['from_email']); ?>">
            </label>
        </div>

        <div class="field">
            <label>
                <span><?php _e('Sender name', $id); ?></span>
                <input type="text" name="from_name" value="<?php echo esc_attr($opts['from_name']); ?>">
            </label>
        </div>

        <div class="button-margin">
            <input type="submit" class="button button-primary" value="<?php _e('Update', $id); ?>">
        </div>
    </form>
    <h3><?php _e('Verification status', $id); ?></h3>
    <p 
    <?php
    if ($opts['verified']) {
        echo 'class="green font-md"';
    } else {
        echo 'class="red font-md"';
    }
    ?>>
    <?php
    if ($opts['verified']) {
        _e('Verified', $id);
        echo ':' . $opts['last_verified_at'];
    } else {
        _e('Failed', $id);
    }
    ?>
    </p>
    <hr />
    <h2><?php _e('Send test message', $id) ?></h2>
    <form name="form2" method="post" action="">
        <?php wp_nonce_field($id, 'send_test_email'); ?>
        <div class="field">
            <label>
                <span><?php _e('Send Email address', $id); ?></span>
                <input type="email" name="send_email"
                       value="<?php echo esc_attr(wp_get_current_user()->user_email); ?>">
            </label>
        </div>

        <div class="button-margin">
            <input type="submit" class="button button-primary" value="<?php _e('Send Email', $id); ?>">
        </div>
    </form>
</div>
