<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Verificar el nonce de seguridad para AJAX
function verificar_nonce() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'domicilios_admin_nonce')) {
        wp_send_json_error('Nonce inválido');
    }
}

// Manejar la solicitud AJAX para agregar un domicilio
function agregar_domicilio() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $nombre = sanitize_text_field($_POST['nombre']);
    $direccion = sanitize_text_field($_POST['direccion']);

    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $result = $wpdb->insert($tabla_domicilios, array(
        'nombre' => $nombre,
        'direccion' => $direccion
    ));

    if ($result !== false) {
        wp_send_json_success('Domicilio añadido con éxito');
    } else {
        wp_send_json_error('Error al añadir el domicilio');
    }
}

// Manejar la solicitud AJAX para editar un domicilio
function editar_domicilio() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $domicilio_id = intval($_POST['domicilio_id']);
    $nombre = sanitize_text_field($_POST['nombre']);
    $direccion = sanitize_text_field($_POST['direccion']);

    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $result = $wpdb->update($tabla_domicilios, array(
        'nombre' => $nombre,
        'direccion' => $direccion
    ), array('id' => $domicilio_id));

    if ($result !== false) {
        wp_send_json_success('Domicilio actualizado con éxito');
    } else {
        wp_send_json_error('Error al actualizar el domicilio');
    }
}

// Manejar la solicitud AJAX para eliminar un domicilio
function eliminar_domicilio() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $domicilio_id = intval($_POST['domicilio_id']);

    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $result = $wpdb->delete($tabla_domicilios, array('id' => $domicilio_id));

    if ($result !== false) {
        wp_send_json_success('Domicilio eliminado con éxito');
    } else {
        wp_send_json_error('Error al eliminar el domicilio');
    }
}

// Hook para manejar las solicitudes AJAX
add_action('wp_ajax_agregar_domicilio', 'agregar_domicilio');
add_action('wp_ajax_editar_domicilio', 'editar_domicilio');
add_action('wp_ajax_eliminar_domicilio', 'eliminar_domicilio');
?>
