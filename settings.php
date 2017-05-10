<?php
register_activation_hook(WPEM4S_PLUGIN_DIR . '/' . WPEM4S_MAIN, 'wpem4s_install');

require_once WPEM4S_PLUGIN_DIR . '/lib/ses.php';

if (is_admin()) {
    include_once WPEM4S_PLUGIN_DIR . '/admin.php' ;
}

function wpem4s_install()
{
    if (get_option(WPEM4S_ID)) {
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

add_action('plugins_loaded', 'wpem4s');

function wpem4s()
{
    load_plugin_textdomain(WPEM4S_ID, false, WPEM4S_ID . '/languages');
}
?>
