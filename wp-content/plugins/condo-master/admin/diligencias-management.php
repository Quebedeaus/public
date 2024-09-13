<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Registrar una nueva diligencia
function registrar_diligencia($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'diligencias';

    $resultado = $wpdb->insert($table_name, array(
        'nombre' => sanitize_text_field($data['nombre']),
        'descripcion' => sanitize_textarea_field($data['descripcion']),
        'fecha' => current_time('mysql')
    ));

    if ($resultado === false) {
        return new WP_Error('registro_error', 'Error al registrar la diligencia.');
    }

    return $wpdb->insert_id;
}

// Actualizar una diligencia existente
function actualizar_diligencia($id, $data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'diligencias';

    $resultado = $wpdb->update($table_name, array(
        'nombre' => sanitize_text_field($data['nombre']),
        'descripcion' => sanitize_textarea_field($data['descripcion'])
    ), array('id' => intval($id)));

    if ($resultado === false) {
        return new WP_Error('actualizacion_error', 'Error al actualizar la diligencia.');
    }

    return true;
}

// Eliminar una diligencia por ID
function eliminar_diligencia($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'diligencias';

    $resultado = $wpdb->delete($table_name, array('id' => intval($id)));

    if ($resultado === false) {
        return new WP_Error('eliminacion_error', 'Error al eliminar la diligencia.');
    }

    return true;
}

// Obtener una diligencia por ID
function obtener_diligencia_por_id($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'diligencias';

    $diligencia = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", intval($id)));

    if (is_null($diligencia)) {
        return new WP_Error('no_encontrado', 'Diligencia no encontrada.');
    }

    return $diligencia;
}

// Obtener una lista de diligencias con parámetros opcionales
function obtener_diligencias($args = array()) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'diligencias';

    $defaults = array(
        'number' => -1, // Obtener todas las diligencias si no se especifica
        'offset' => 0
    );
    $args = wp_parse_args($args, $defaults);

    $query = $wpdb->prepare("SELECT * FROM $table_name ORDER BY fecha DESC LIMIT %d OFFSET %d", intval($args['number']), intval($args['offset']));
    $diligencias = $wpdb->get_results($query);

    return $diligencias;
}
?>

