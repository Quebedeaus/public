<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Función para obtener personas
function obtener_personas($limit = -1, $offset = 0) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $query = $wpdb->prepare(
        "SELECT * FROM $tabla_personas LIMIT %d OFFSET %d",
        $limit,
        $offset
    );
    return $wpdb->get_results($query);
}

// Función para obtener una persona por ID
function obtener_persona_por_id($id) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $query = $wpdb->prepare("SELECT * FROM $tabla_personas WHERE id = %d", $id);
    return $wpdb->get_row($query);
}

// Función para contar todas las personas
function contar_personas() {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    return $wpdb->get_var("SELECT COUNT(*) FROM $tabla_personas");
}

// Función para agregar una nueva persona
function registrar_persona($datos) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';

    $datos = array(
        'nombre' => sanitize_text_field($datos['nombre']),
        'telefono' => sanitize_text_field($datos['telefono']),
        'domicilio_id' => intval($datos['domicilio_id']),
    );

    $formatos = array('%s', '%s', '%d');

    return $wpdb->insert($tabla_personas, $datos, $formatos);
}

// Función para actualizar una persona
function actualizar_persona($id, $datos) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';

    $datos = array(
        'nombre' => sanitize_text_field($datos['nombre']),
        'telefono' => sanitize_text_field($datos['telefono']),
        'domicilio_id' => intval($datos['domicilio_id']),
    );

    $formatos = array('%s', '%s', '%d');

    return $wpdb->update($tabla_personas, $datos, array('id' => $id), $formatos, array('%d'));
}

// Función para eliminar una persona
function eliminar_persona($id) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    return $wpdb->delete($tabla_personas, array('id' => $id), array('%d'));
}
?>
