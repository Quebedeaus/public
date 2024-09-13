<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Función para agregar una nueva persona a la base de datos
function agregar_persona($data) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $resultado = $wpdb->insert(
        $tabla_personas,
        $data,
        array('%s', '%s', '%s') // Tipos de datos: %s = string
    );
    
    if ($resultado === false) {
        return new WP_Error('db_insert_error', 'Error al agregar la persona a la base de datos.');
    }
    
    return $wpdb->insert_id; // Retorna el ID de la nueva persona
}

// Función para actualizar una persona existente en la base de datos
function actualizar_persona($id, $data) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $resultado = $wpdb->update(
        $tabla_personas,
        $data,
        array('id' => $id),
        array('%s', '%s', '%s'), // Tipos de datos para los datos
        array('%d') // Tipo de datos para la condición
    );
    
    if ($resultado === false) {
        return new WP_Error('db_update_error', 'Error al actualizar la persona en la base de datos.');
    }
    
    return true;
}

// Función para obtener una persona por ID
function obtener_persona_por_id($id) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $persona = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $tabla_personas WHERE id = %d",
        $id
    ));
    
    if ($persona === null) {
        return new WP_Error('no_persona', 'No se encontró la persona.');
    }
    
    return $persona;
}

// Función para obtener todas las personas
function obtener_personas() {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $personas = $wpdb->get_results("SELECT * FROM $tabla_personas");
    
    return $personas;
}
?>
