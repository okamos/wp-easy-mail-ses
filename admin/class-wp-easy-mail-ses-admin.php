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
            'WP easy mail SES',
            'WP easy mail SES',
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

        if (!empty($_POST)
            && isset($_POST['verify_email'])
            && check_admin_referer($this->plugin_name, 'verify_email')
        ) {
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
            $this->_show_notice(__('Success Update', $this->plugin_name));
        }
        if (!empty($_POST)
            && isset($_POST['send_test_email'])
            && check_admin_referer($this->plugin_name, 'send_test_email')
        ) {
            $subject = __('Test Subject', $this->plugin_name);
            $message = __('Test Message', $this->plugin_name);
            $sent = wp_mail($_POST['send_email'], $subject, $message);

            if ($sent) {
                $this->_show_notice(__('Success Send', $this->plugin_name));
            } else {
                $this->_show_notice(__('Failed Send', $this->plugin_name), 'error');
            }
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

    /**
     * Display the message with a white background and a green / yellow / red /
     * blue left border.
     *
     * @param string $message Message to show to user
     * @param string $status  success / warning / error / info
     *
     * @return void
     */
    private function _show_notice($message, $status = 'success')
    {
        ?>
        <div class="notice notice-<?php echo $status ?> is-dismissible">
            <p><?php echo $message ?></p>
        </div>
        <?php
    }
}
?>
