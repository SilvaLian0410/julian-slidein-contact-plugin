<?php
if (!defined('ABSPATH')) { exit; }

function jsi_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'jsi_entries';
}

function jsi_create_table() {
    global $wpdb;
    $table = jsi_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        first_name VARCHAR(190) NOT NULL,
        last_name VARCHAR(190) NOT NULL,
        email VARCHAR(190) NOT NULL,
        page_url TEXT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY email (email)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function jsi_insert_entry($data) {
    global $wpdb;
    $table = jsi_table_name();

    $wpdb->insert(
        $table,
        [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'page_url'   => $data['page_url'],
            'created_at' => current_time('mysql', true),
        ],
        ['%s','%s','%s','%s','%s']
    );
}

function jsi_get_entries($args = []) {
    global $wpdb;
    $defaults = [
        'per_page' => 20,
        'paged'    => 1,
    ];
    $args = wp_parse_args($args, $defaults);
    $per_page = max(1, (int) $args['per_page']);
    $paged = max(1, (int) $args['paged']);
    $offset = ($paged - 1) * $per_page;

    $table = jsi_table_name();
    $items = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table ORDER BY created_at DESC LIMIT %d OFFSET %d", $per_page, $offset),
        ARRAY_A
    );
    $total = (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");

    return [
        'items' => $items,
        'total' => $total,
        'per_page' => $per_page,
        'paged' => $paged,
    ];
}

function jsi_entries_exist() {
    global $wpdb;
    $table = jsi_table_name();
    $sql = $wpdb->prepare("SHOW TABLES LIKE %s", $table);
    $result = $wpdb->get_var($sql);
    return $result === $table;
}


