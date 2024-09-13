<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Obtener todos los paquetes
function obtener_paquetes($limit = -1, $offset = 0) {
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';

    $query = "SELECT * FROM $tabla_paquetes";
    if ($limit > 0) {
        $query .= " LIMIT %d OFFSET %d";
    }

    $query = $wpdb->prepare($query, $limit, $offset);
    $result = $wpdb->get_results($query);

    return $result;
}

// Obtener un paquete por ID
function obtener_paquete_por_id($paquete_id) {
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';

    $query = $wpdb->prepare("SELECT * FROM $tabla_paquetes WHERE id = %d", $paquete_id);
    $result = $wpdb->get_row($query);

    return $result;
}

// Contar el número total de paquetes
function contar_paquetes() {
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';

    $query = "SELECT COUNT(*) FROM $tabla_paquetes";
    $count = $wpdb->get_var($query);

    return $count;
}

// Registrar un nuevo paquete
function registrar_paquete($datos_paquete) {
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';

    $resultado = $wpdb->insert($tabla_paquetes, $datos_paquete);

    return $resultado;
}

// Actualizar un paquete existente
function actualizar_paquete($paquete_id, $datos_paquete) {
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';

    $resultado = $wpdb->update($tabla_paquetes, $datos_paquete, array('id' => $paquete_id));

    return $resultado;
}

// Eliminar un paquete
function eliminar_paquete($paquete_id) {
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';

    $resultado = $wpdb->delete($tabla_paquetes, array('id' => $paquete_id));

    return $resultado;
}
?>
