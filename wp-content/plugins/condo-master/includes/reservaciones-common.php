<?php
// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

// Crear tabla de reservas en la activación del plugin
register_activation_hook(__FILE__, 'create_reservaciones_table');

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

// Función para obtener la información del usuario (ejemplo)
function condo_master_get_user_info($user_id) {
    // Obtener información del usuario
    $user_info = get_userdata($user_id);
    return $user_info;
}
?>
