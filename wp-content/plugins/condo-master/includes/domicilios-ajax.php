<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Manejar la solicitud AJAX para añadir un nuevo domicilio
function handle_add_domicilio() {
    // Verificar nonce
    check_ajax_referer('domicilios_admin_nonce', 'nonce');

    // Obtener datos del request
    $nombre = sanitize_text_field($_POST['nombre']);
    $direccion = sanitize_textarea_field($_POST['direccion']);
    $telefono = sanitize_text_field($_POST['telefono']);

    // Validar datos
    if (empty($nombre) || empty($direccion)) {
        wp_send_json_error(array('message' => 'Nombre y dirección son requeridos.'));
    }

    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    // Insertar nuevo domicilio
    $resultado = $wpdb->insert(
        $tabla_domicilios,
        array(
            'nombre' => $nombre,
            'direccion' => $direccion,
            'telefono' => $telefono
        ),
        array(
            '%s',
            '%s',
            '%s'
        )
    );

    if ($resultado === false) {
        wp_send_json_error(array('message' => 'No se pudo añadir el domicilio.'));
    }

    wp_send_json_success(array('message' => 'Domicilio añadido con éxito.'));
}

// Manejar la solicitud AJAX para editar un domicilio
function handle_edit_domicilio() {
    // Verificar nonce
    check_ajax_referer('domicilios_admin_nonce', 'nonce');

    // Obtener datos del request
    $id = intval($_POST['id']);
    $nombre = sanitize_text_field($_POST['nombre']);
    $direccion = sanitize_textarea_field($_POST['direccion']);
    $telefono = sanitize_text_field($_POST['telefono']);

    // Validar datos
    if (empty($id) || empty($nombre) || empty($direccion)) {
        wp_send_json_error(array('message' => 'ID, nombre y dirección son requeridos.'));
    }

    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    // Actualizar domicilio
    $resultado = $wpdb->update(
        $tabla_domicilios,
        array(
            'nombre' => $nombre,
            'direccion' => $direccion,
            'telefono' => $telefono
        ),
        array('id' => $id),
        array(
            '%s',
            '%s',
            '%s'
        ),
        array('%d')
    );

    if ($resultado === false) {
        wp_send_json_error(array('message' => 'No se pudo actualizar el domicilio.'));
    }

    wp_send_json_success(array('message' => 'Domicilio actualizado con éxito.'));
}

// Manejar la solicitud AJAX para eliminar un domicilio
function handle_delete_domicilio() {
    // Verificar nonce
    check_ajax_referer('domicilios_admin_nonce', 'nonce');

    // Obtener datos del request
    $id = intval($_POST['id']);

    // Validar datos
    if (empty($id)) {
        wp_send_json_error(array('message' => 'ID es requerido.'));
    }

    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    // Eliminar domicilio
    $resultado = $wpdb->delete(
        $tabla_domicilios,
        array('id' => $id),
        array('%d')
    );

    if ($resultado === false) {
        wp_send_json_error(array('message' => 'No se pudo eliminar el domicilio.'));
    }

    wp_send_json_success(array('message' => 'Domicilio eliminado con éxito.'));
}

// Hook para manejar las solicitudes AJAX
add_action('wp_ajax_add_domicilio', 'handle_add_domicilio');
add_action('wp_ajax_edit_domicilio', 'handle_edit_domicilio');
add_action('wp_ajax_delete_domicilio', 'handle_delete_domicilio');
?>
