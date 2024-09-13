<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Función para agregar una nueva persona
function agregar_persona($data) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $resultado = $wpdb->insert(
        $tabla_personas,
        $data,
        array('%s', '%s', '%s')
    );

    if ($resultado === false) {
        return new WP_Error('error_db', 'Error al agregar la persona.');
    }
    return $wpdb->insert_id;
}

// Función para actualizar una persona existente
function actualizar_persona($id, $data) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $resultado = $wpdb->update(
        $tabla_personas,
        $data,
        array('id' => $id),
        array('%s', '%s', '%s'),
        array('%d')
    );

    if ($resultado === false) {
        return new WP_Error('error_db', 'Error al actualizar la persona.');
    }
    return $resultado;
}

// Función para eliminar una persona
function eliminar_persona($id) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $resultado = $wpdb->delete(
        $tabla_personas,
        array('id' => $id),
        array('%d')
    );

    if ($resultado === false) {
        return new WP_Error('error_db', 'Error al eliminar la persona.');
    }
    return $resultado;
}

// Función para obtener los datos de una persona por ID
function obtener_persona_por_id($id) {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $persona = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_personas WHERE id = %d", $id));
    if (is_null($persona)) {
        return new WP_Error('no_persona', 'Persona no encontrada.');
    }
    return $persona;
}

// Función para obtener todas las personas
function obtener_todas_las_personas() {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    
    $personas = $wpdb->get_results("SELECT * FROM $tabla_personas");
    if (is_wp_error($personas)) {
        return new WP_Error('error_db', 'Error al obtener las personas.');
    }
    return $personas;
}

// Función para crear la tabla de personas si no existe
function crear_tabla_personas() {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_personas (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        telefono varchar(20) DEFAULT '' NOT NULL,
        domicilio varchar(255) DEFAULT '' NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// Ejecutar la creación de la tabla al activar el plugin
function activar_plugin_personas() {
    crear_tabla_personas();
}
register_activation_hook(__FILE__, 'activar_plugin_personas');
?>
