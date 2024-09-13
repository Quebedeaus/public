<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Obtener todos los domicilios
function obtener_domicilios() {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    $query = "SELECT * FROM $tabla_domicilios";
    return $wpdb->get_results($query);
}

// Obtener un domicilio por ID
function obtener_domicilio_por_id($domicilio_id) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    $query = $wpdb->prepare("SELECT * FROM $tabla_domicilios WHERE id = %d", $domicilio_id);
    return $wpdb->get_row($query);
}

// Obtener todas las personas con paginación
function obtener_personas($limit = 20, $offset = 0) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $query = $wpdb->prepare("SELECT * FROM $tabla_personas LIMIT %d OFFSET %d", $limit, $offset);
    return $wpdb->get_results($query);
}

// Contar todas las personas
function contar_personas() {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    return $wpdb->get_var("SELECT COUNT(*) FROM $tabla_personas");
}

// Obtener una persona por ID
function obtener_persona_por_id($person_id) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $query = $wpdb->prepare("SELECT * FROM $tabla_personas WHERE id = %d", $person_id);
    return $wpdb->get_row($query);
}

// Registrar una nueva persona
function registrar_persona($persona_data) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $resultado = $wpdb->insert($tabla_personas, $persona_data);
    if ($resultado === false) {
        return new WP_Error('insert_failed', 'No se pudo registrar la persona.');
    }
    return true;
}

// Actualizar una persona
function actualizar_persona($person_id, $persona_data) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $resultado = $wpdb->update($tabla_personas, $persona_data, array('id' => $person_id));
    if ($resultado === false) {
        return new WP_Error('update_failed', 'No se pudo actualizar la persona.');
    }
    return true;
}

// Eliminar una persona
function eliminar_persona($person_id) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $resultado = $wpdb->delete($tabla_personas, array('id' => $person_id));
    if ($resultado === false) {
        return new WP_Error('delete_failed', 'No se pudo eliminar la persona.');
    }
    return true;
}
?>
