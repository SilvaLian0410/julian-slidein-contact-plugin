<?php
if (!defined('ABSPATH')) { exit; }

function jsi_handle_submit() {
    check_ajax_referer('jsi_submit_nonce', 'nonce');

    $settings = jsi_get_settings();
    $recaptcha_enabled = !empty($settings['recaptcha_enabled']);
    // If reCAPTCHA is enabled, require keys and verify token
    if ($recaptcha_enabled) {
        if (empty($settings['recaptcha_site_key']) || empty($settings['recaptcha_secret_key'])) {
            wp_send_json_error(['message' => __('reCAPTCHA is not configured. Please contact the site administrator.', 'julian-slide-in')]);
        }
        $token = isset($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : '';
        if (!$token) {
            wp_send_json_error(['message' => __('Please complete the reCAPTCHA challenge.', 'julian-slide-in')]);
        }
        $resp = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $settings['recaptcha_secret_key'],
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ],
            'timeout' => 10,
        ]);
        if (is_wp_error($resp)) {
            $msg = $resp->get_error_message();
            wp_send_json_error(['message' => sprintf(__('reCAPTCHA verification failed: %s', 'julian-slide-in'), $msg)]);
        }
        $data = json_decode(wp_remote_retrieve_body($resp), true);
        if (empty($data['success'])) {
            wp_send_json_error(['message' => __('reCAPTCHA invalid.', 'julian-slide-in')]);
        }
    }

    // Build message
    $fields = [];
    $entry_payload = [
        'first_name' => '',
        'last_name' => '',
        'email' => '',
        'page_url' => '',
    ];

    foreach ($settings['form_fields'] as $f) {
        $name = $f['name'];
        $label = $f['label'];
        $val = isset($_POST[$name]) ? wp_unslash($_POST[$name]) : '';
        $val = is_string($val) ? sanitize_textarea_field($val) : '';
        if (!empty($f['required']) && $val === '') {
            wp_send_json_error(['message' => sprintf(__('%s is required.', 'julian-slide-in'), $label)]);
        }
        $fields[] = [
            'label' => $label,
            'value' => $val,
        ];

        if ($name === 'email') {
            $entry_payload['email'] = sanitize_email($val);
        } elseif ($name === 'first_name') {
            $entry_payload['first_name'] = $val;
        } elseif ($name === 'last_name') {
            $entry_payload['last_name'] = $val;
        }
    }

    if (empty($entry_payload['first_name']) || empty($entry_payload['last_name']) || empty($entry_payload['email'])) {
        wp_send_json_error(['message' => __('Please complete the required fields.', 'julian-slide-in')]);
    }

    if (!is_email($entry_payload['email'])) {
        wp_send_json_error(['message' => __('Please enter a valid email address.', 'julian-slide-in')]);
    }

    $subject = sprintf(__('New message from %s', 'julian-slide-in'), get_bloginfo('name'));
    $body = "";
    foreach ($fields as $item) {
        $body .= $item['label'] . ": " . $item['value'] . "\n";
    }
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    $entry_payload['page_url'] = isset($_POST['page_url']) ? esc_url_raw(wp_unslash($_POST['page_url'])) : '';
    if (function_exists('jsi_insert_entry')) {
        jsi_insert_entry($entry_payload);
    }

    $sent = wp_mail($settings['target_email'], $subject, $body, $headers);
    if ($sent) {
        wp_send_json_success(['message' => $settings['success_message']]);
    } else {
        $fallback = __('Email could not be sent. Please configure SMTP or contact support.', 'julian-slide-in');
        wp_send_json_error(['message' => $fallback]);
    }
}
add_action('wp_ajax_jsi_submit', 'jsi_handle_submit');
add_action('wp_ajax_nopriv_jsi_submit', 'jsi_handle_submit');

?>