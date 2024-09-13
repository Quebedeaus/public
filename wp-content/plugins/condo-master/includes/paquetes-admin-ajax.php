<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Verificar el nonce de seguridad para AJAX
function verificar_nonce() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'paquetes_admin_nonce')) {
        wp_send_json_error('Nonce inválido');
    }
}

// Manejar la solicitud AJAX para agregar un paquete
function agregar_paquete() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $nombre = sanitize_text_field($_POST['nombre']);
    $descripcion = sanitize_textarea_field($_POST['descripcion']);
    $precio = floatval($_POST['precio']);

    // Validar el precio
    if ($precio <= 0) {
        wp_send_json_error('Precio inválido');
    }

    // Insertar el paquete
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';
    $resultado = $wpdb->insert($tabla_paquetes, array(
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'precio' => $precio
    ));

    if ($resultado) {
        wp_send_json_success('Paquete añadido con éxito');
    } else {
        wp_send_json_error('Error al añadir el paquete');
    }
}

// Manejar la solicitud AJAX para editar un paquete
function editar_paquete() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $paquete_id = intval($_POST['paquete_id']);
    $nombre = sanitize_text_field($_POST['nombre']);
    $descripcion = sanitize_textarea_field($_POST['descripcion']);
    $precio = floatval($_POST['precio']);

    // Validar el precio
    if ($precio <= 0) {
        wp_send_json_error('Precio inválido');
    }

    // Actualizar el paquete
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';
    $resultado = $wpdb->update($tabla_paquetes, array(
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'precio' => $precio
    ), array('id' => $paquete_id));

    if ($resultado !== false) {
        wp_send_json_success('Paquete actualizado con éxito');
    } else {
        wp_send_json_error('Error al actualizar el paquete');
    }
}

// Manejar la solicitud AJAX para eliminar un paquete
function eliminar_paquete() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $paquete_id = intval($_POST['paquete_id']);

    // Eliminar el paquete
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';
    $resultado = $wpdb->delete($tabla_paquetes, array('id' => $paquete_id));

    if ($resultado) {
        wp_send_json_success('Paquete eliminado con éxito');
    } else {
        wp_send_json_error('Error al eliminar el paquete');
    }
}

// Hook para manejar las solicitudes AJAX
add_action('wp_ajax_agregar_paquete', 'agregar_paquete');
add_action('wp_ajax_editar_paquete', 'editar_paquete');
add_action('wp_ajax_eliminar_paquete', 'eliminar_paquete');
?>
