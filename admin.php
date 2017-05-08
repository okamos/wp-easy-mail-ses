<?php
add_action('admin_menu', 'wpem4s_admin_menu');

function wpem4s_admin_menu()
{
    add_options_page('SES easy mail', 'SES easy mail', 'manage_options', 'wpem4s-settings', 'wpem4s_options');
}

function wpem4s_options()
{
    if (!current_user_can('manage_options')) {
        wp_die(_e('You do not have sufficient permissions to access this page', WPEM4S_ID));
    }

    wp_enqueue_style(
        WPEM4S_ID . 'admin', WPEM4S_ID . '/css/styles.css'
    );
    include_once WPEM4S_PLUGIN_DIR . '/tmpl/admin.php' ;
}
?>
