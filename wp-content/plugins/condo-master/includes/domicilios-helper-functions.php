<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Función para obtener domicilios
function obtener_domicilios($limit = -1, $offset = 0) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    $query = $wpdb->prepare(
        "SELECT * FROM $tabla_domicilios LIMIT %d OFFSET %d",
        $limit,
        $offset
    );
    return $wpdb->get_results($query);
}

// Función para obtener un domicilio por ID
function obtener_domicilio_por_id($id) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    $query = $wpdb->prepare("SELECT * FROM $tabla_domicilios WHERE id = %d", $id);
    return $wpdb->get_row($query);
}

// Función para contar todos los domicilios
function contar_domicilios() {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    return $wpdb->get_var("SELECT COUNT(*) FROM $tabla_domicilios");
}

// Función para agregar un nuevo domicilio
function registrar_domicilio($datos) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $datos = array(
        'nombre' => sanitize_text_field($datos['nombre']),
        'direccion' => sanitize_textarea_field($datos['direccion']),
        'ciudad' => sanitize_text_field($datos['ciudad']),
        'estado' => sanitize_text_field($datos['estado']),
        'codigo_postal' => sanitize_text_field($datos['codigo_postal']),
    );

    $formatos = array('%s', '%s', '%s', '%s', '%s');

    return $wpdb->insert($tabla_domicilios, $datos, $formatos);
}

// Función para actualizar un domicilio
function actualizar_domicilio($id, $datos) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $datos = array(
        'nombre' => sanitize_text_field($datos['nombre']),
        'direccion' => sanitize_textarea_field($datos['direccion']),
        'ciudad' => sanitize_text_field($datos['ciudad']),
        'estado' => sanitize_text_field($datos['estado']),
        'codigo_postal' => sanitize_text_field($datos['codigo_postal']),
    );

    $formatos = array('%s', '%s', '%s', '%s', '%s');

    return $wpdb->update($tabla_domicilios, $datos, array('id' => $id), $formatos, array('%d'));
}

// Función para eliminar un domicilio
function eliminar_domicilio($id) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    return $wpdb->delete($tabla_domicilios, array('id' => $id), array('%d'));
}

// Función para obtener el nombre de un domicilio por ID
function get_domicilio_name($id) {
    $domicilio = obtener_domicilio_por_id($id);
    return $domicilio ? $domicilio->nombre : 'Desconocido';
}
?>
