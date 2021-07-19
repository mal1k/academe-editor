<?php
/*
Plugin Name: LearnDash LMS - Notifications (onsite)
Description: Allows to store LearnDash notifications in the DB and show them to users in the frontend.
Version: 1.0
Author: Volodymyr H.
 */

/**
 * Find the modified code in original add-on by "Custom code start" string
 */

define( 'ONSITE_NOTIFICATIONS_PLUGIN_DIR', __DIR__ . '/' );
define( 'ONSITE_NOTIFICATIONS_PLUGIN_VERSION', '1.0' );
define( 'ONSITE_NOTIFICATIONS_DB_VERSION', '1.0' );
define( 'ONSITE_NOTIFICATIONS_DB_TABLE', 'ld_notifications' );

require_once ONSITE_NOTIFICATIONS_PLUGIN_DIR . 'includes/database.php';
require_once ONSITE_NOTIFICATIONS_PLUGIN_DIR . 'includes/functions.php';
require_once ONSITE_NOTIFICATIONS_PLUGIN_DIR . 'includes/acf.php';

add_action( 'wp_enqueue_scripts', 'ld_onsite_notifications_load_assets' );
function ld_onsite_notifications_load_assets() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'ld-onsite-notifications-styles',
        $plugin_url . 'assets/css/ld-onsite-notifications-styles.css',
        [],
        ONSITE_NOTIFICATIONS_PLUGIN_VERSION );
}

register_activation_hook( __FILE__, 'create_db_tables' );