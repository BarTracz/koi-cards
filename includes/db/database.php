<?php

// Don't allow direct access to this file.
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create the custom database table for Koi Cards on plugin activation.
 */
function koi_cards_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'koi_cards';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name tinytext NOT NULL,
        card_image varchar(255) NOT NULL,
        black_overlay_image varchar(255) NOT NULL,
        foil_mask_image varchar(255) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
