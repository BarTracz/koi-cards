<?php

/**
 * Plugin Name: KoiCards
 * Description: A plugin to create cards for KoiCorp
 * Version: 1.0
 * Author: KoiCorp
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

define('KOI_CARDS_PATH', plugin_dir_path(__FILE__));
define('KOI_CARDS_URL', plugin_dir_url(__FILE__));

require_once KOI_CARDS_PATH . 'includes/front-display/koi-card-front-display.php';
require_once KOI_CARDS_PATH . 'includes/db/database.php';
require_once KOI_CARDS_PATH . 'includes/forms/admin-page.php';

// Activation hook to create the database table.
register_activation_hook(__FILE__, 'koi_cards_create_table');