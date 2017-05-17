<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @category Class
 * @package  WpEasyMailSES
 * @author   Shinichi Okamoto <himinato.k@gmail.com>
 * @license  GPL-2.0+ http://www.gnu.org/licenses/gpl-2.0.html
 * @link     https://github.com/okamos/wp-easy-mail-ses
 */
class WpEasyMailSES {
    /**
     * Define the core functionality of the plugin.
     */
    public function __construct()
    {
        $this->plugin_name = 'wp-easy-mail-ses';
        $this->version = '0.1.0';

        $this->_load_dependencies();
        $this->_set_locale();
        $this->_define_admin_hooks();
    }

    /**
     * Load the reuqired dependencies for this plugin.
     *
     * @return void
     */
    private function _load_dependencies()
    {
        include_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
        if (is_admin()) {
            include_once plugin_dir_path(__FILE__) . 'admin/class-wp-easy-mail-ses-admin.php';
        }
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @return void
     */
    private function _set_locale()
    {
        add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @return void
     */
    public function load_plugin_textdomain()
    {
        load_plugin_textdomain(
            $this->plugin_name,
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }

    /**
     * Register all of the hooks related to the admin area functionality.
     *
     * @return void
     */
    private function _define_admin_hooks()
    {
        if (is_admin()) {
            $admin = new WpEasyMailSESAdmin($this->plugin_name);

            add_action('admin_enqueue_scripts', array($admin, 'enqueue_styles'));
            add_action('admin_menu', array($admin, 'add_option_page'));
        }
    }

    /**
     *  Run the core function.
     *
     *  @return void
     */
    public function run()
    {
        if (!function_exists('wp_mail')) {
            function wp_mail($to, $subject, $message, $headers = '', $attachments = '') {
                $opts = get_option('wp-easy-mail-ses');

                if (!$opts['verified']) {
                    return false;
                }
                $htmlMessage = '';
                $charset = 'UTF-8';

                $ses = new SimpleEmailService(
                    $opts['access_key'], $opts['secret_key'], $opts['region']
                );
                if ($opts['from_name']) {
                    $from = $opts['from_name'] . '<' . $opts['from_email'] . '>';
                } else {
                    $from = $opts['from_email'];
                }

                if ($headers != '') {
                    if (is_array($headers)) {
                        $headers = implode("\r\n", $headers);
                    }
                    $delimiter = "\r\n";
                    if (strpos($headers, $delimiter) == false) {
                        $delimiter = "\n";
                    }
                    $headers = explode($delimiter, $headers);

                    foreach ($headers as $header) {
                        if (preg_match('/^From: (.*)/i', $header)) {
                            $from = trim(substr($header, 5));
                        } elseif (preg_match('/^Content-Type:/i', $header)) {
                            $exploded = explode(' ', $header);
                            $contentType = rtrim($exploded[1], ';');
                            if (strtolower($contentType) == 'text/html') {
                                $htmlMessage = $message;
                            }
                            $charset = substr($exploded[2], 8);
                        }
                    }
                }

                $envelope = new SimpleEmailServiceEnvelope(
                    $from, $subject, $message, $htmlMessage
                );
                $envelope->addTo($to);
                $envelope->setCharset($charset);

                if ($attachments != '') {
                    if (is_string($attachments)) {
                        $attachments = explode("\n", $attachments);
                    }
                    foreach ($attachments as $attachment) {
                        $envelope->addAttachmentFromFile(basename($attachment), $attachment);
                    }
                }

                if (is_array($headers)) {
                    foreach ($headers as $header) {
                        if (preg_match('/^To:/i', $header)) {
                            $envelope->addTo(trim(substr($header, 3)));
                        } elseif (preg_match('/^Cc:/i', $header)) {
                            $envelope->addCc(trim(substr($header, 3)));
                        } elseif (preg_match('/^Bcc:/i', $header)) {
                            $envelope->addBcc(trim(substr($header, 4)));
                        } elseif (preg_match('/^Reply-To:/i', $header)) {
                            $envelope->addReplyTo(trim(substr($header, 9)));
                        }
                    }
                }

                $requestId = $ses->sendEmail($envelope);

                if (isset($requestId->code)) {
                    return false;
                }
                return true;
            }
        }
    }
}
?>
