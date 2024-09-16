<?php
// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

function create_condominios_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'condominios';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        direccion text NOT NULL,
        superadmin_id bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'create_condominios_table');
?>
