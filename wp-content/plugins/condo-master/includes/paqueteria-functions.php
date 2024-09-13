<?php
// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

// Función para registrar una nueva entrega.
function registrar_entrega($datos) {
    global $wpdb;
    $tabla = $wpdb->prefix . 'paqueteria';
    
    // Validación de datos
    $datos = array_map('sanitize_text_field', $datos);
    if (empty($datos['residente_id']) || empty($datos['paquete'])) {
        return new WP_Error('campos_vacios', 'Todos los campos son obligatorios.');
    }
    
    $resultado = $wpdb->insert($tabla, $datos);
    if ($resultado === false) {
        return new WP_Error('error_db', 'Error al insertar en la base de datos.');
    }
    return $wpdb->insert_id;
}

// Función para obtener todas las entregas.
function obtener_entregas() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'paqueteria';
    $resultados = $wpdb->get_results("SELECT * FROM $tabla ORDER BY fecha_entrega DESC");
    if ($resultados === null) {
        return new WP_Error('error_db', 'Error al obtener las entregas.');
    }
    return $resultados;
}

// Función para obtener una entrega por ID.
function obtener_entrega_por_id($id) {
    global $wpdb;
    $tabla = $wpdb->prefix . 'paqueteria';
    $id = intval($id);
    $resultado = $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla WHERE id = %d", $id));
    if ($resultado === null) {
        return new WP_Error('no_encontrado', 'Entrega no encontrada.');
    }
    return $resultado;
}

// Generar número consecutivo.
function generate_consecutive_number() {
    global $wpdb;
    $tabla = $wpdb->prefix . 'paqueteria';
    $last_id = $wpdb->get_var("SELECT MAX(id) FROM $tabla");
    return intval($last_id) + 1;
}

// Obtener el teléfono del residente.
function get_resident_phone($residente_id) {
    global $wpdb;
    $tabla_residentes = $wpdb->prefix . 'residentes';

    $residente_id = intval($residente_id);
    $telefono = $wpdb->get_var($wpdb->prepare(
        "SELECT telefono FROM $tabla_residentes WHERE id = %d",
        $residente_id
    ));

    if ($telefono === null) {
        return new WP_Error('telefono_no_encontrado', 'Teléfono del residente no encontrado.');
    }

    return $telefono;
}

// Función para registrar una nueva entrega de paquetería.
function registrar_entrega_paqueteria($datos) {
    global $wpdb;
    $tabla_paqueteria = $wpdb->prefix . 'paqueteria';

    if (empty($datos['residente_id']) || empty($datos['paquete'])) {
        return new WP_Error('campos_vacios', 'Todos los campos son obligatorios.');
    }

    $telefono = get_resident_phone($datos['residente_id']);
    if (is_wp_error($telefono)) {
        return $telefono;
    }

    $resultado = $wpdb->insert($tabla_paqueteria, array(
        'residente_id' => $datos['residente_id'],
        'paquete'      => $datos['paquete'],
        'fecha_entrega'=> current_time('mysql'),
        'status'       => 'pendiente'
    ));

    if ($resultado === false) {
        return new WP_Error('error_db', 'Error al insertar en la base de datos.');
    }
    return $wpdb->insert_id;
}

// Función para obtener todas las entregas de un residente.
function obtener_entregas_residente($residente_id) {
    global $wpdb;
    $tabla_paqueteria = $wpdb->prefix . 'paqueteria';

    $residente_id = intval($residente_id);
    $resultados = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $tabla_paqueteria WHERE residente_id = %d ORDER BY fecha_entrega DESC", 
        $residente_id
    ));

    if ($resultados === null) {
        return new WP_Error('error_db', 'Error al obtener las entregas del residente.');
    }
    return $resultados;
}

// Función para eliminar un paquete.
function cm_delete_package($package_id) {
    global $wpdb;
    $tabla_paqueteria = $wpdb->prefix . 'paqueteria';

    $package_id = intval($package_id);
    $resultado = $wpdb->delete($tabla_paqueteria, array('id' => $package_id), array('%d'));

    if ($resultado === false) {
        error_log('Error al eliminar paquete: ' . $wpdb->last_error);
        return new WP_Error('error_db', 'Error al eliminar el paquete.');
    }
    return $resultado;
}

// Función para obtener todos los paquetes.
function cm_get_all_packages() {
    global $wpdb;
    $tabla_paqueteria = $wpdb->prefix . 'paqueteria';

    $resultados = $wpdb->get_results("SELECT * FROM $tabla_paqueteria ORDER BY fecha_entrega DESC");
    if ($resultados === null) {
        return new WP_Error('error_db', 'Error al obtener los paquetes.');
    }
    return $resultados;
}

// Función para añadir un paquete.
function cm_add_package($data) {
    global $wpdb;
    $tabla_paqueteria = $wpdb->prefix . 'paqueteria';

    if (empty($data['residente_id']) || empty($data['paquete'])) {
        return new WP_Error('campos_vacios', 'Todos los campos son obligatorios.');
    }

    $result = $wpdb->insert($tabla_paqueteria, $data);
    if ($result === false) {
        error_log('Error al añadir paquete: ' . $wpdb->last_error);
        return new WP_Error('error_db', 'Error al añadir paquete.');
    }
    return $wpdb->insert_id;
}
