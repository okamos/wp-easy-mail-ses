<?php
register_activation_hook(WPEM4S_PLUGIN_DIR . '/' . WPEM4S_MAIN, 'wpem4s_install');

if (is_admin()) {
    add_action('init', 'wpem4s_init');
    include_once WPEM4S_PLUGIN_DIR . '/admin.php' ;
}

function wpem4s_init()
{
    $domain = 'wp-easy-mail-ses';
    load_plugin_textdomain('wp-easy-mail-ses', false, basename(dirname(__FILE__)));
}

function wpem4s_install()
{
    if (get_option(WPEM4S_IDENTIFIER)) {
        return;
    }

    add_option(
        WPEM4S_IDENTIFIER, array(
            'from_email' => '',
            'from_name' => 'Information',
            'access_key' => '',
            'secret_key' => '',
            'region' => 'us-east-1'
        )
    );
}
?>
