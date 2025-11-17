<?php
/*
Plugin Name: Julian Slide-In Contact
Description: Slide-in contact form widget with admin-configurable fields, styles, and Google reCAPTCHA.
Version: 1.0.0
Author: Julian
*/

if (!defined('ABSPATH')) {
    exit;
}

define('JSI_VERSION', '1.1.0');
define('JSI_PLUGIN_FILE', __FILE__);
define('JSI_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JSI_PLUGIN_URL', plugin_dir_url(__FILE__));

// Option key
const JSI_OPTION_KEY = 'jsi_settings';

function jsi_get_default_form_fields() {
    return [
        ['name' => 'first_name', 'label' => __('First Name', 'julian-slide-in'), 'type' => 'text', 'required' => true],
        ['name' => 'last_name', 'label' => __('Last Name', 'julian-slide-in'), 'type' => 'text', 'required' => true],
        ['name' => 'email', 'label' => __('Email Address', 'julian-slide-in'), 'type' => 'email', 'required' => true],
    ];
}

function jsi_default_settings() {
    return [
        'enabled' => true,
        'position' => 'right', // left|right
        'primary_color' => '#8E2DE2',
        'accent_color' => '#E14D2A',
        'form_bg_color' => '#ffffff',
        'form_text_color' => '#1f2937',
        'button_label' => 'Get In Touch',
        'launcher_style' => 'icon', // button|icon
        'launcher_icon_class' => 'fa-solid fa-envelope-open-text',
        'target_email' => get_option('admin_email'),
        'recaptcha_enabled' => true,
        'recaptcha_site_key' => '',
        'recaptcha_secret_key' => '',
        'form_fields' => jsi_get_default_form_fields(),
        'success_message' => 'Thanks! We will contact you shortly.',
        'failure_message' => 'Sorry, something went wrong. Please try again.',
    ];
}

function jsi_activate() {
    $defaults = jsi_default_settings();
    $existing = get_option(JSI_OPTION_KEY);
    if (!$existing || !is_array($existing)) {
        update_option(JSI_OPTION_KEY, $defaults);
    } else {
        // Merge any missing keys to keep upgrades safe
        update_option(JSI_OPTION_KEY, wp_parse_args($existing, $defaults));
    }

    jsi_create_table();
}
register_activation_hook(__FILE__, 'jsi_activate');

function jsi_maybe_update_tables() {
    if (!jsi_entries_exist()) {
        jsi_create_table();
    }
}
add_action('plugins_loaded', 'jsi_maybe_update_tables');

// Helpers
function jsi_get_settings() {
    $settings = get_option(JSI_OPTION_KEY, []);
    return wp_parse_args($settings, jsi_default_settings());
}

// Includes
require_once JSI_PLUGIN_DIR . 'includes/admin.php';
require_once JSI_PLUGIN_DIR . 'includes/frontend.php';
require_once JSI_PLUGIN_DIR . 'includes/ajax.php';
require_once JSI_PLUGIN_DIR . 'includes/entries.php';

// i18n placeholder (domain matches folder)
function jsi_load_textdomain() {
    load_plugin_textdomain('julian-slide-in', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'jsi_load_textdomain');

?>