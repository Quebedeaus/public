<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Crear la tabla de domicilios si no existe
function crear_tabla_domicilios() {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_domicilios (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        direccion varchar(255) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Registrar un nuevo domicilio
function registrar_domicilio($data) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $result = $wpdb->insert($tabla_domicilios, array(
        'nombre' => sanitize_text_field($data['nombre']),
        'direccion' => sanitize_text_field($data['direccion'])
    ));

    return $result ? true : $wpdb->last_error;
}

// Actualizar un domicilio existente
function actualizar_domicilio($id, $data) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $result = $wpdb->update($tabla_domicilios, array(
        'nombre' => sanitize_text_field($data['nombre']),
        'direccion' => sanitize_text_field($data['direccion'])
    ), array('id' => $id));

    return $result !== false ? true : $wpdb->last_error;
}

// Eliminar un domicilio
function eliminar_domicilio($id) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $result = $wpdb->delete($tabla_domicilios, array('id' => $id));

    return $result !== false ? true : $wpdb->last_error;
}

// Obtener domicilios
function obtener_domicilios() {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    return $wpdb->get_results("SELECT * FROM $tabla_domicilios");
}

// Obtener un domicilio por ID
function obtener_domicilio_por_id($id) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_domicilios WHERE id = %d", $id));
}
?>
