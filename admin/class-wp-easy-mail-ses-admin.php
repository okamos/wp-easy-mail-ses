<?php

/**
 * The admin-specific functionality on the plugin.
 *
 * @category Class
 * @package  WpEasyMailSES
 * @author   Shinichi Okamoto <himinato.k@gmail.com>
 * @license  GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://github.com/okamos/wp-easy-mail-ses
 */
class WpEasyMailSESAdmin
{
    /**
     * Set properties.
     *
     * @param string $plugin_name the plugin name
     */
    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @return void
     */
    public function enqueue_styles()
    {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/' . $this->plugin_name . '-admin.css'
        );
    }

    /**
     * Register the option page.
     *
     * @return void
     */
    public function add_option_page()
    {
        add_options_page(
            'SES easy mail',
            'SES easy mail',
            'manage_options',
            'wpem4s-settings',
            array($this, 'add_page')
        );
    }

    /**
     * Define the page.
     *
     * @return void
     */
    public function add_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(_e('You do not have sufficient permissions to access this page', $this->plugin_name));
        }

        $opts = get_option($this->plugin_name);
        $update_name = 'update_credentials';

        if (!empty($_POST) && check_admin_referer('verify_email', 'wpem4s')) {
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
                $opts['verified'] = $this->_verify_email($opts);
                $opts['last_verified_at'] = date_i18n('Y-m-d H:i:s');
            }

            update_option($this->plugin_name, $opts);
        }
        include_once plugin_dir_path(__FILE__) . 'partials/' . $this->plugin_name . '-admin-display.php';
    }

    /**
     * Verify the Email address via SES GetIdentityVerificationAttributes API.
     *
     * @param object $opts option value. it is retrieved get_option($plugin_name).
     *
     * @return boolean
     */
    private function _verify_email($opts)
    {
        if (!filter_var($opts['from_email'], FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $ses = new SimpleEmailService(
            $opts['access_key'], $opts['secret_key'], $opts['region']
        );
        try {
            $entries = $ses->getIdentityVerificationAttributes(
                array(
                    $opts['from_email'],
                    explode('@', $opts['from_email'])[1] // extract domain
                )
            );
        } catch(Exception $e) {
            return false;
        }
        if (isset($entries->code)) {
            return false;
        }
        $verified = false;

        foreach ($entries as $entry) {
            if ($entry['Status'] == 'Success') {
                $verified = true;
            }
        }
        return $verified;
    }
}
?>
