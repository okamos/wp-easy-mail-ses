<?php

/**
 * The plugin bootstrap file
 *
 * @package WpEasyMailSES
 *
 * @wordpress-plugin
 * Plugin Name: WP Easy mail for SES
 * Plugin URI: https://github.com/okamos/wp-easy-mail-ses
 * Description: Drop-in replacement in wp_mail using the AWS SES.
 * Version: 0.1.0
 * Author: Shinichi Okamoto
 * Author URI: https://github.com/okamos
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * The code that runs during plugin activation.
 *
 * @return void
 */
function activate_wp_easy_mail_ses()
{
    if (get_option('wp-easy-mail-ses')) {
        return;
    }

    add_option(
        WPEM4S_ID, array(
            'access_key' => '',
            'secret_key' => '',
            'region' => 'us-east-1',
            'from_email' => '',
            'from_name' => 'Information',
            'verified' => false,
            'last_verified_at' => ''
        )
    );
}

/**
 * The code that runs during plugin deactivation.
 *
 * @return void
 */
function deactivate_wp_easy_mail_ses()
{
    /* do nothing */
}

register_activation_hook(__FILE__, 'activate_wp_easy_mail_ses');
register_deactivation_hook(__FILE__, 'deactivate_wp_easy_mail_ses');

require_once plugin_dir_path(__FILE__) . 'class-wp-easy-mail-ses.php';

/**
 * Begins execution of the plugin.
 *
 * @return void
 */
function run_wp_easy_mail_ses()
{
    $plugin = new WpEasyMailSES();
    $plugin->run();
}
run_wp_easy_mail_ses();
?>
