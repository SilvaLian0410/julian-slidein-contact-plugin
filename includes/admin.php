<?php
if (!defined('ABSPATH')) { exit; }

// Admin menu and settings
function jsi_get_icon_choices() {
    return [
        'fa-solid fa-envelope-open-text' => __('Envelope (Open)', 'julian-slide-in'),
        'fa-solid fa-paper-plane' => __('Paper Plane', 'julian-slide-in'),
        'fa-solid fa-bell' => __('Bell', 'julian-slide-in'),
        'fa-solid fa-bolt' => __('Lightning Bolt', 'julian-slide-in'),
        'fa-solid fa-comments' => __('Chat Bubbles', 'julian-slide-in'),
        'fa-solid fa-handshake' => __('Handshake', 'julian-slide-in'),
        'fa-solid fa-heart' => __('Heart', 'julian-slide-in'),
        'fa-solid fa-star' => __('Star', 'julian-slide-in'),
        'fa-solid fa-gift' => __('Gift', 'julian-slide-in'),
        'fa-solid fa-lightbulb' => __('Lightbulb', 'julian-slide-in'),
        'fa-solid fa-users' => __('Users', 'julian-slide-in'),
        'fa-solid fa-user-astronaut' => __('Astronaut', 'julian-slide-in'),
    ];
}

function jsi_register_settings() {
    register_setting('jsi_settings_group', JSI_OPTION_KEY, [
        'type' => 'array',
        'sanitize_callback' => 'jsi_sanitize_settings',
        'default' => jsi_default_settings(),
    ]);

    add_settings_section('jsi_main_section', __('General', 'julian-slide-in'), function () {
        echo '<p>' . esc_html__('Configure the slide-in contact form.', 'julian-slide-in') . '</p>';
    }, 'jsi_settings');

    add_settings_field('jsi_enabled', __('Enabled', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        printf('<input type="checkbox" name="%s[enabled]" value="1" %s />', esc_attr(JSI_OPTION_KEY), checked(!empty($s['enabled']), true, false));
    }, 'jsi_settings', 'jsi_main_section');

    add_settings_field('jsi_position', __('Position', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        $options = ['left' => __('Left', 'julian-slide-in'), 'right' => __('Right', 'julian-slide-in')];
        echo '<select name="' . esc_attr(JSI_OPTION_KEY) . '[position]">';
        foreach ($options as $val => $label) {
            printf('<option value="%s" %s>%s</option>', esc_attr($val), selected($s['position'], $val, false), esc_html($label));
        }
        echo '</select>';
    }, 'jsi_settings', 'jsi_main_section');

    add_settings_field('jsi_colors', __('Colors', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        printf('<label>%s <input type="text" class="jsi-color" name="%s[primary_color]" value="%s" /></label> ',
            esc_html__('Primary', 'julian-slide-in'),
            esc_attr(JSI_OPTION_KEY),
            esc_attr($s['primary_color'])
        );
        printf('<label style="margin-left:12px">%s <input type="text" class="jsi-color" name="%s[accent_color]" value="%s" /></label> ',
            esc_html__('Accent', 'julian-slide-in'),
            esc_attr(JSI_OPTION_KEY),
            esc_attr($s['accent_color'])
        );
        echo '<br /><br />';
        printf('<label>%s <input type="text" class="jsi-color" name="%s[form_bg_color]" value="%s" /></label> ',
            esc_html__('Panel Background', 'julian-slide-in'),
            esc_attr(JSI_OPTION_KEY),
            esc_attr($s['form_bg_color'])
        );
        printf('<label style="margin-left:12px">%s <input type="text" class="jsi-color" name="%s[form_text_color]" value="%s" /></label>',
            esc_html__('Panel Text', 'julian-slide-in'),
            esc_attr(JSI_OPTION_KEY),
            esc_attr($s['form_text_color'])
        );
    }, 'jsi_settings', 'jsi_main_section');

    add_settings_field('jsi_email', __('Send To Email', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        printf('<input type="email" style="width:320px" name="%s[target_email]" value="%s" />', esc_attr(JSI_OPTION_KEY), esc_attr($s['target_email']));
    }, 'jsi_settings', 'jsi_main_section');

    add_settings_field('jsi_button_label', __('Button Label', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        printf('<input type="text" style="width:320px" name="%s[button_label]" value="%s" />', esc_attr(JSI_OPTION_KEY), esc_attr($s['button_label']));
    }, 'jsi_settings', 'jsi_main_section');

    add_settings_section('jsi_launcher_section', __('Launcher', 'julian-slide-in'), function () {
        echo '<p>' . esc_html__('Choose how the opener appears (text button or icon).', 'julian-slide-in') . '</p>';
    }, 'jsi_settings');

    add_settings_field('jsi_launcher_style', __('Style', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        $options = ['button' => __('Button', 'julian-slide-in'), 'icon' => __('Icon', 'julian-slide-in')];
        echo '<select name="' . esc_attr(JSI_OPTION_KEY) . '[launcher_style]">';
        foreach ($options as $val => $label) {
            printf('<option value="%s" %s>%s</option>', esc_attr($val), selected($s['launcher_style'], $val, false), esc_html($label));
        }
        echo '</select>';
    }, 'jsi_settings', 'jsi_launcher_section');

    add_settings_field('jsi_launcher_icon', __('Launcher Icon', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        $val = esc_attr($s['launcher_icon_class']);
        $name = esc_attr(JSI_OPTION_KEY) . '[launcher_icon_class]';
        $choices = jsi_get_icon_choices();
        if ($val && !isset($choices[$val])) {
            $choices = [$val => __('Saved icon', 'julian-slide-in')] + $choices;
        }
        echo '<div class="jsi-icon-picker">';
        echo '<select id="jsi_icon_class" class="jsi-icon-select" name="' . $name . '">';
        foreach ($choices as $class => $label) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($class),
                selected($val, $class, false),
                esc_html($label)
            );
        }
        echo '</select>';
        echo '<span class="jsi-icon-preview-wrap"><i id="jsi-icon-preview" class="jsi-icon-preview fa-fw ' . esc_attr($s['launcher_icon_class']) . '"></i></span>';
        echo '</div>';
        echo '<p class="description">' . esc_html__('Pick an icon directly from the list. More presets can be added via code filters if needed.', 'julian-slide-in') . '</p>';
    }, 'jsi_settings', 'jsi_launcher_section');

    add_settings_section('jsi_recaptcha_section', __('Google reCAPTCHA', 'julian-slide-in'), function () {
        echo '<p>' . esc_html__('Enable or disable reCAPTCHA v2 Checkbox for testing, and enter keys.', 'julian-slide-in') . '</p>';
    }, 'jsi_settings');

    add_settings_field('jsi_recaptcha_enabled', __('Enable reCAPTCHA', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        printf('<input type="checkbox" name="%s[recaptcha_enabled]" value="1" %s />', esc_attr(JSI_OPTION_KEY), checked(!empty($s['recaptcha_enabled']), true, false));
        echo '<p class="description">' . esc_html__('Uncheck to temporarily disable reCAPTCHA for testing.', 'julian-slide-in') . '</p>';
    }, 'jsi_settings', 'jsi_recaptcha_section');

    add_settings_field('jsi_recaptcha_site', __('Site Key', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        printf('<input type="text" style="width:420px" name="%s[recaptcha_site_key]" value="%s" />', esc_attr(JSI_OPTION_KEY), esc_attr($s['recaptcha_site_key']));
    }, 'jsi_settings', 'jsi_recaptcha_section');

    add_settings_field('jsi_recaptcha_secret', __('Secret Key', 'julian-slide-in'), function () {
        $s = jsi_get_settings();
        printf('<input type="text" style="width:420px" name="%s[recaptcha_secret_key]" value="%s" />', esc_attr(JSI_OPTION_KEY), esc_attr($s['recaptcha_secret_key']));
    }, 'jsi_settings', 'jsi_recaptcha_section');

}
add_action('admin_init', 'jsi_register_settings');

function jsi_sanitize_settings($settings) {
    $defaults = jsi_default_settings();
    // Read raw incoming values FIRST so unchecked checkboxes don't get overridden by defaults
    $incoming = (array)$settings;
    $incoming['enabled'] = !empty($incoming['enabled']);
    $incoming['recaptcha_enabled'] = !empty($incoming['recaptcha_enabled']);

    // Merge with defaults after normalizing checkboxes
    $settings = wp_parse_args($incoming, $defaults);
    $settings['position'] = in_array($settings['position'], ['left', 'right'], true) ? $settings['position'] : 'right';
    $settings['primary_color'] = sanitize_hex_color($settings['primary_color']) ?: $defaults['primary_color'];
    $settings['accent_color'] = sanitize_hex_color($settings['accent_color']) ?: $defaults['accent_color'];
    $settings['form_bg_color'] = sanitize_hex_color($settings['form_bg_color']) ?: $defaults['form_bg_color'];
    $settings['form_text_color'] = sanitize_hex_color($settings['form_text_color']) ?: $defaults['form_text_color'];
    $settings['button_label'] = sanitize_text_field($settings['button_label']);
    $settings['launcher_style'] = in_array($settings['launcher_style'], ['button','icon'], true) ? $settings['launcher_style'] : 'icon';
    $settings['launcher_icon_class'] = sanitize_text_field($settings['launcher_icon_class']);
    $settings['target_email'] = sanitize_email($settings['target_email']);
    $settings['recaptcha_site_key'] = sanitize_text_field($settings['recaptcha_site_key']);
    $settings['recaptcha_secret_key'] = sanitize_text_field($settings['recaptcha_secret_key']);
    // recaptcha_enabled already normalized via $incoming

    $settings['form_fields'] = jsi_get_default_form_fields();

    $settings['success_message'] = sanitize_text_field($settings['success_message']);
    $settings['failure_message'] = sanitize_text_field($settings['failure_message']);

    return $settings;
}

function jsi_admin_menu() {
    $cap = 'manage_options';
    add_menu_page(
        __('Julian Slide-In', 'julian-slide-in'),
        __('Julian Slide-In', 'julian-slide-in'),
        $cap,
        'jsi_entries',
        'jsi_render_entries_page',
        'dashicons-feedback',
        58
    );

    add_submenu_page(
        'jsi_entries',
        __('Submissions', 'julian-slide-in'),
        __('Submissions', 'julian-slide-in'),
        $cap,
        'jsi_entries',
        'jsi_render_entries_page'
    );

    add_submenu_page(
        'jsi_entries',
        __('Settings', 'julian-slide-in'),
        __('Settings', 'julian-slide-in'),
        $cap,
        'jsi_settings',
        'jsi_render_settings_page'
    );
}
add_action('admin_menu', 'jsi_admin_menu');

function jsi_admin_assets($hook) {
    $screens = [
        'toplevel_page_jsi_entries',
        'jsi_entries_page_jsi_settings',
    ];

    if (!in_array($hook, $screens, true)) {
        return;
    }

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('jsi-admin', JSI_PLUGIN_URL . 'assets/admin.js', ['jquery','wp-color-picker'], JSI_VERSION, true);
    wp_enqueue_style('jsi-admin', JSI_PLUGIN_URL . 'assets/admin.css', [], JSI_VERSION);
    wp_enqueue_style('jsi-fa-admin', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], '6.5.2');
}
add_action('admin_enqueue_scripts', 'jsi_admin_assets');

function jsi_render_settings_page() {
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Julian Slide-In Settings', 'julian-slide-in') . '</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('jsi_settings_group');
    do_settings_sections('jsi_settings');
    submit_button();
    echo '</form>';
    echo '</div>';
}

function jsi_render_entries_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Slide-In Submissions', 'julian-slide-in') . '</h1>';

    if (!jsi_entries_exist()) {
        echo '<p>' . esc_html__('Submission storage table is missing. Reactivate the plugin to recreate it.', 'julian-slide-in') . '</p>';
        echo '</div>';
        return;
    }

    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $result = jsi_get_entries([
        'paged' => $paged,
        'per_page' => $per_page,
    ]);
    $items = $result['items'];
    $total = $result['total'];
    $total_pages = $per_page ? ceil($total / $per_page) : 1;

    if (empty($items)) {
        echo '<p>' . esc_html__('No submissions yet. Your slide-in form entries will appear here.', 'julian-slide-in') . '</p>';
        echo '</div>';
        return;
    }

    echo '<table class="widefat striped">';
    echo '<thead><tr>';
    echo '<th>' . esc_html__('Date', 'julian-slide-in') . '</th>';
    echo '<th>' . esc_html__('First Name', 'julian-slide-in') . '</th>';
    echo '<th>' . esc_html__('Last Name', 'julian-slide-in') . '</th>';
    echo '<th>' . esc_html__('Email', 'julian-slide-in') . '</th>';
    echo '<th>' . esc_html__('Page URL', 'julian-slide-in') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($items as $entry) {
        echo '<tr>';
        echo '<td>' . esc_html(get_date_from_gmt($entry['created_at'], get_option('date_format') . ' ' . get_option('time_format'))) . '</td>';
        echo '<td>' . esc_html($entry['first_name']) . '</td>';
        echo '<td>' . esc_html($entry['last_name']) . '</td>';
        echo '<td><a href="mailto:' . esc_attr($entry['email']) . '">' . esc_html($entry['email']) . '</a></td>';
        if (!empty($entry['page_url'])) {
            echo '<td><a href="' . esc_url($entry['page_url']) . '" target="_blank" rel="noopener noreferrer">' . esc_html($entry['page_url']) . '</a></td>';
        } else {
            echo '<td><em>' . esc_html__('Unknown', 'julian-slide-in') . '</em></td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';

    if ($total_pages > 1) {
        echo '<div class="tablenav"><div class="tablenav-pages">';
        echo paginate_links([
            'base' => add_query_arg('paged', '%#%'),
            'format' => '',
            'current' => $paged,
            'total' => $total_pages,
        ]);
        echo '</div></div>';
    }

    echo '</div>';
}

?>