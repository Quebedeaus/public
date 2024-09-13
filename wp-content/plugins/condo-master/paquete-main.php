<?php
/*
Plugin Name: Paquete Plugin
Description: Un plugin para gestionar paquetería.
Version: 1.0
Author: Tu Nombre
*/

// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

// Incluir archivos necesarios.
require_once plugin_dir_path(__FILE__) . 'includes/paqueteria-common.php';
require_once plugin_dir_path(__FILE__) . 'includes/paqueteria-functions.php';
require_once plugin_dir_path(__FILE__) . 'admin/paqueteria-admin.php';
require_once plugin_dir_path(__FILE__) . 'admin/paqueteria-settings.php';
require_once plugin_dir_path(__FILE__) . 'public/paqueteria-public.php';

// Registrar hooks de activación y desactivación.
register_activation_hook(__FILE__, 'create_reservaciones_table');
register_deactivation_hook(__FILE__, 'delete_reservaciones_table');

// Función de activación.
function create_reservaciones_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservaciones';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        date date NOT NULL,
        time time NOT NULL,
        details text NOT NULL,
        status varchar(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Función de desactivación.
function delete_reservaciones_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservaciones';
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
?>
