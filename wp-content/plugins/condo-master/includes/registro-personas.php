<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
global $wpdb;
$table_name = $wpdb->prefix . 'personas';

// Crear tabla si no existe
function crear_tabla_personas() {
    global $wpdb;
    global $table_name;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        nombre VARCHAR(255) NOT NULL,
        telefono VARCHAR(20),
        domicilio VARCHAR(255),
        fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('plugins_loaded', 'crear_tabla_personas');

// Agregar nueva persona
function agregar_persona($data) {
    global $wpdb;
    global $table_name;

    $resultado = $wpdb->insert(
        $table_name,
        array(
            'nombre' => sanitize_text_field($data['nombre']),
            'telefono' => sanitize_text_field($data['telefono']),
            'domicilio' => sanitize_text_field($data['domicilio'])
        ),
        array('%s', '%s', '%s')
    );

    if ($resultado === false) {
        return new WP_Error('error_db', 'Error al agregar la persona.');
    }

    return $wpdb->insert_id;
}

// Obtener persona por ID
function obtener_persona_por_id($id) {
    global $wpdb;
    global $table_name;

    $persona = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));

    if (empty($persona)) {
        return new WP_Error('error_no_found', 'Persona no encontrada.');
    }

    return $persona;
}

// Actualizar persona
function actualizar_persona($id, $data) {
    global $wpdb;
    global $table_name;

    $resultado = $wpdb->update(
        $table_name,
        array(
            'nombre' => sanitize_text_field($data['nombre']),
            'telefono' => sanitize_text_field($data['telefono']),
            'domicilio' => sanitize_text_field($data['domicilio'])
        ),
        array('id' => $id),
        array('%s', '%s', '%s'),
        array('%d')
    );

    if ($resultado === false) {
        return new WP_Error('error_db', 'Error al actualizar la persona.');
    }

    return $resultado;
}

// Eliminar persona
function eliminar_persona($id) {
    global $wpdb;
    global $table_name;

    $resultado = $wpdb->delete(
        $table_name,
        array('id' => $id),
        array('%d')
    );

    if ($resultado === false) {
        return new WP_Error('error_db', 'Error al eliminar la persona.');
    }

    return $resultado;
}

// Obtener todas las personas
function obtener_todas_las_personas() {
    global $wpdb;
    global $table_name;

    return $wpdb->get_results("SELECT * FROM $table_name");
}
?>
