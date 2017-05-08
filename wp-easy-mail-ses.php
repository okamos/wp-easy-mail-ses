<?php
/*
Plugin Name: WP Easy mail for SES
Plugin URI: https://github.com/okamos/wp-easy-mail-ses
Description: Drop-in replacement in wp_mail using the AWS SES.
Version: 0.1.0
Author: Shinichi Okamoto
Author URI: https://github.com/okamos
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
define('WPEM4S_ID', 'wp-easy-mail-ses');

define('WPEM4S_VERSION', '0.1.0');

define('WPEM4S_MAIN', basename(__FILE__));

define('WPEM4S_PLUGIN_DIR', dirname(__FILE__));

define('WPEM4S_PLUGIN_URL', plugins_url(basename(__FILE__, '.php')));

require_once WPEM4S_PLUGIN_DIR . '/settings.php';
?>
