<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Definir el nombre de la tabla como una constante
define('PAQUETERIA_TABLE', $wpdb->prefix . 'paqueteria');

// Funciones relacionadas con la paquetería

/**
 * Obtiene los detalles de un paquete específico.
 *
 * @param int $package_id ID del paquete.
 * @return object|null Objeto con los detalles del paquete o null si no se encuentra.
 */
function cm_get_package_details($package_id) {
    global $wpdb;
    $package = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . PAQUETERIA_TABLE . " WHERE id = %d", $package_id));
    return $package;
}

// Puedes agregar más funciones relacionadas con la paquetería aquí
?>
