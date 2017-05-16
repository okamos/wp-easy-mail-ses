<?php
register_activation_hook(WPEM4S_PLUGIN_DIR . '/' . WPEM4S_MAIN, 'wpem4s_install');

require_once WPEM4S_PLUGIN_DIR . '/vendor/autoload.php';

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

function wpem4s_mail($to, $subject, $message, $headers, $attachments) {
    $opts = get_option(WPEM4S_ID);
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
            error_log($attachment);
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

if (!function_exists('wp_mail')) {
    // override wp_mail
    function wp_mail($to, $subject, $message, $headers = '', $attachments = '') {
        return wpem4s_mail($to, $subject, $message, $headers, $attachments);
    }
}

function wpem4s_verify_ses($opts) {
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
?>
