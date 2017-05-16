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

    $opts = get_option(WPEM4S_ID);
    $update_name = 'update_credentials';

    if (isset($_POST[$update_name])) {
        $update_params = [
            'access_key',
            'secret_key',
            'region',
            'from_email',
            'from_name'
        ];

        foreach ($update_params as $param) {
            if (isset($_POST[$param]) && strlen($_POST[$param]) > 0) {
                $opts[$param] = $_POST[$param];
            }
        }

        if ($opts['access_key']
            && $opts['secret_key']
            && $opts['region']
            && $opts['from_email']
        ) {
            $opts['verified'] = wpem4s_verify_ses($opts);
            $opts['last_verified_at'] = date_i18n('Y-m-d H:i:s');
        }

        update_option(WPEM4S_ID, $opts);
    }

    wp_enqueue_style(
        WPEM4S_ID . '-admin', WPEM4S_PLUGIN_URL . '/css/styles.css'
    );
    include_once WPEM4S_PLUGIN_DIR . '/tmpl/admin.php' ;
}
?>
