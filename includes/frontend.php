<?php
if (!defined('ABSPATH')) { exit; }

function jsi_enqueue_assets() {
    $s = jsi_get_settings();
    if (empty($s['enabled'])) { return; }
    wp_enqueue_style('jsi-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap', [], null);
    wp_enqueue_style('jsi-frontend', JSI_PLUGIN_URL . 'assets/style.css', [], JSI_VERSION);
    wp_enqueue_style('jsi-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', [], '6.5.2');
    wp_enqueue_script('jsi-frontend', JSI_PLUGIN_URL . 'assets/script.js', ['jquery'], JSI_VERSION, true);

    $data = [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('jsi_submit_nonce'),
        'messages' => [
            'success' => $s['success_message'],
            'failure' => $s['failure_message'],
        ],
    ];
    wp_localize_script('jsi-frontend', 'JSI', $data);

    if (!empty($s['recaptcha_enabled']) && !empty($s['recaptcha_site_key'])) {
        wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', [], null, true);
    }
}
add_action('wp_enqueue_scripts', 'jsi_enqueue_assets');

function jsi_render_widget() {
    $s = jsi_get_settings();
    if (empty($s['enabled'])) { return; }

    $posClass = $s['position'] === 'left' ? 'jsi-left' : 'jsi-right';
    $primary = esc_attr($s['primary_color']);
    $accent = esc_attr($s['accent_color']);
    $bg = esc_attr($s['form_bg_color']);
    $text = esc_attr($s['form_text_color']);

    $style_attr = sprintf(
        'style="--jsi-primary:%s;--jsi-accent:%s;--jsi-form-bg:%s;--jsi-form-text:%s;"',
        $primary,
        $accent,
        $bg,
        $text
    );

    echo '<div id="jsi-slidein" class="' . esc_attr($posClass) . '" ' . $style_attr . '>';
    $launcher_class = 'jsi-trigger';
    $launcher_style = isset($s['launcher_style']) ? $s['launcher_style'] : 'button';
    if ($launcher_style === 'icon') { $launcher_class .= ' jsi-icon-only'; }
    echo '  <button class="' . esc_attr($launcher_class) . '" aria-label="' . esc_attr__('Open contact form', 'julian-slide-in') . '">';
    if ($launcher_style === 'icon') {
        $icon_class = !empty($s['launcher_icon_class']) ? $s['launcher_icon_class'] : 'fa-solid fa-envelope-open-text';
        echo '<i class="fa-fw ' . esc_attr($icon_class) . '"></i>';
    } else {
        echo '<span class="jsi-trigger-label">' . esc_html($s['button_label']) . '</span>';
    }
    echo '</button>';
    // Backdrop and sliding container with close button (matches design.html)
    echo '  <div class="jsi-backdrop" aria-hidden="true"></div>';
    echo '  <div class="jsi-container">';
    echo '    <button class="jsi-close-btn" aria-label="Close">'
        . '<svg class="jsi-close-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">'
        . '<line x1="18" y1="6" x2="6" y2="18" />'
        . '<line x1="6" y1="6" x2="18" y2="18" />'
        . '</svg>'
        . '</button>';
    echo '    <div class="jsi-panel" role="dialog" aria-modal="true" aria-labelledby="jsi-title">';
    echo '      <div class="jsi-header">';
    echo '      <h3 id="jsi-title">' . esc_html__('Stay in the loop', 'julian-slide-in') . '</h3>';
    echo '      <p>' . esc_html__('Leave your details and we’ll keep you updated.', 'julian-slide-in') . '</p>';
    echo '    </div>';
    echo '    <form id="jsi-form" class="jsi-form">';
    foreach ($s['form_fields'] as $f) {
        $name = esc_attr($f['name']);
        $label = esc_html($f['label']);
        $required = !empty($f['required']);
        echo '<div class="jsi-field">';
        echo '<label class="jsi-form-label" for="jsi-' . $name . '">' . $label . ($required ? ' <span class="jsi-required">*</span>' : '') . '</label>';
        if ($f['type'] === 'textarea') {
            printf('<textarea class="jsi-form-control" id="jsi-%1$s" name="%1$s" %2$s></textarea>', $name, $required ? 'required' : '');
        } else {
            printf('<input class="jsi-form-control" id="jsi-%1$s" type="%2$s" name="%1$s" %3$s />', $name, esc_attr($f['type']), $required ? 'required' : '');
        }
        echo '</div>';
    }

    if (!empty($s['recaptcha_enabled']) && !empty($s['recaptcha_site_key'])) {
        echo '<div class="jsi-field jsi-recaptcha">';
        echo '<div class="g-recaptcha" data-sitekey="' . esc_attr($s['recaptcha_site_key']) . '"></div>';
        echo '</div>';
    }

    echo '      <div class="jsi-actions">';
    echo '        <button type="submit" class="jsi-submit">' . esc_html__('Send Now', 'julian-slide-in') . '</button>';
    echo '      </div>';
    echo '      <div class="jsi-status" aria-live="polite"></div>';
    echo '    </form>';
    echo '    </div>'; // .jsi-panel
    echo '  </div>'; // .jsi-container
    echo '</div>';
}
add_action('wp_footer', 'jsi_render_widget');

?>