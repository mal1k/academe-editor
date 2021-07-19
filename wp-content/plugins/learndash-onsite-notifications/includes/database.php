<?php
function create_db_tables() {
    create_ld_notifications_table();
    add_option( 'plugin_learndash_onsite_notifications_db_version', ONSITE_NOTIFICATIONS_DB_VERSION );
}

function create_ld_notifications_table() {

    global $wpdb;

    $table_name = $wpdb->prefix . ONSITE_NOTIFICATIONS_DB_TABLE;

    $charset_collate = $wpdb->get_charset_collate();

    if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
        $charset = 'utf8mb4';
        $collate = 'utf8mb4_unicode_ci';
        $charset_collate = "DEFAULT CHARACTER SET {$charset} COLLATE {$collate}";
    }

    $sql = "CREATE TABLE $table_name (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
		title varchar(500) NOT NULL,
		message text NOT NULL,
		icon bigint(20) NOT NULL,
		link varchar(128),
        shortcode_data varchar(1000),
        sent_on varchar(20) NOT NULL,
        sent_on_readable varchar(20) NOT NULL,
        read_on varchar(20),
        read_on_readable varchar(20),
		PRIMARY KEY  (id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

}