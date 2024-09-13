<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Obtener todas las diligencias con paginación
function obtener_diligencias($args = array()) {
    global $wpdb;

    $defaults = array(
        'number' => -1, // Número ilimitado de resultados
        'offset' => 0
    );
    $args = wp_parse_args($args, $defaults);

    $query = $wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}diligencias
        LIMIT %d, %d
    ", $args['offset'], $args['number']);

    return $wpdb->get_results($query);
}

// Contar el total de diligencias
function contar_diligencias() {
    global $wpdb;

    $query = "SELECT COUNT(*) FROM {$wpdb->prefix}diligencias";
    return $wpdb->get_var($query);
}

// Obtener una diligencia por ID
function obtener_diligencia_por_id($diligencia_id) {
    global $wpdb;

    $diligencia_id = intval($diligencia_id);
    $query = $wpdb->prepare("
        SELECT * FROM {$wpdb->prefix}diligencias
        WHERE id = %d
    ", $diligencia_id);

    return $wpdb->get_row($query);
}

// Registrar una nueva diligencia
function registrar_diligencia($diligencia_data) {
    global $wpdb;

    $result = $wpdb->insert(
        "{$wpdb->prefix}diligencias",
        array(
            'nombre' => sanitize_text_field($diligencia_data['nombre']),
            'descripcion' => sanitize_textarea_field($diligencia_data['descripcion']),
            'fecha' => current_time('mysql')
        ),
        array('%s', '%s', '%s')
    );

    if ($result === false) {
        return new WP_Error('diligencia_error', 'Error al registrar la diligencia.');
    }

    return true;
}

// Actualizar una diligencia existente
function actualizar_diligencia($diligencia_id, $diligencia_data) {
    global $wpdb;

    $result = $wpdb->update(
        "{$wpdb->prefix}diligencias",
        array(
            'nombre' => sanitize_text_field($diligencia_data['nombre']),
            'descripcion' => sanitize_textarea_field($diligencia_data['descripcion'])
        ),
        array('id' => intval($diligencia_id)),
        array('%s', '%s'),
        array('%d')
    );

    if ($result === false) {
        return new WP_Error('diligencia_error', 'Error al actualizar la diligencia.');
    }

    return true;
}

// Eliminar una diligencia
function eliminar_diligencia($diligencia_id) {
    global $wpdb;

    $result = $wpdb->delete(
        "{$wpdb->prefix}diligencias",
        array('id' => intval($diligencia_id)),
        array('%d')
    );

    if ($result === false) {
        return new WP_Error('diligencia_error', 'Error al eliminar la diligencia.');
    }

    return true;
}

// Crear la tabla de diligencias en la activación del plugin
function crear_tabla_diligencias() {
    global $wpdb;

    $tabla_diligencias = $wpdb->prefix . 'diligencias';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_diligencias (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        descripcion text NOT NULL,
        fecha datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Borrar la tabla de diligencias al desactivar el plugin
function borrar_tabla_diligencias() {
    global $wpdb;

    $tabla_diligencias = $wpdb->prefix . 'diligencias';

    $sql = "DROP TABLE IF EXISTS $tabla_diligencias;";
    $wpdb->query($sql);
}

// Activar el plugin: crear tabla
register_activation_hook(__FILE__, 'crear_tabla_diligencias');

// Desactivar el plugin: borrar tabla
register_deactivation_hook(__FILE__, 'borrar_tabla_diligencias');
?>
