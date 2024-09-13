<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Función para crear la tabla de diligencias si no existe
function crear_tabla_diligencias() {
    global $wpdb;
    $tabla_diligencias = $wpdb->prefix . 'diligencias';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_diligencias (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        descripcion text,
        fecha datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Función para obtener diligencias
function obtener_diligencias($args = array()) {
    global $wpdb;
    $tabla_diligencias = $wpdb->prefix . 'diligencias';

    $defaults = array(
        'number' => -1,
        'offset' => 0,
        'orderby' => 'fecha',
        'order' => 'DESC'
    );
    $args = wp_parse_args($args, $defaults);

    $query = $wpdb->prepare(
        "SELECT * FROM $tabla_diligencias
        ORDER BY %s %s
        LIMIT %d OFFSET %d",
        $args['orderby'],
        $args['order'],
        $args['number'],
        $args['offset']
    );

    return $wpdb->get_results($query);
}

// Función para registrar una nueva diligencia
function registrar_diligencia($data) {
    global $wpdb;
    $tabla_diligencias = $wpdb->prefix . 'diligencias';

    $result = $wpdb->insert($tabla_diligencias, array(
        'nombre' => sanitize_text_field($data['nombre']),
        'descripcion' => sanitize_textarea_field($data['descripcion']),
        'fecha' => current_time('mysql')
    ));

    return $result !== false;
}

// Función para actualizar una diligencia existente
function actualizar_diligencia($id, $data) {
    global $wpdb;
    $tabla_diligencias = $wpdb->prefix . 'diligencias';

    $result = $wpdb->update($tabla_diligencias, array(
        'nombre' => sanitize_text_field($data['nombre']),
        'descripcion' => sanitize_textarea_field($data['descripcion'])
    ), array('id' => intval($id)));

    return $result !== false;
}

// Función para eliminar una diligencia
function eliminar_diligencia($id) {
    global $wpdb;
    $tabla_diligencias = $wpdb->prefix . 'diligencias';

    $result = $wpdb->delete($tabla_diligencias, array('id' => intval($id)));

    return $result !== false;
}
?>
