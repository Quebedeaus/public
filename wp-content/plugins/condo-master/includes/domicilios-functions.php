<?php
// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

// Crear la tabla de domicilios si no existe.
function crear_tabla_domicilios() {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    $charset_collate = $wpdb->get_charset_collate();

    // Se agrega condominio_id para relacionar los domicilios con condominios.
    $sql = "CREATE TABLE $tabla_domicilios (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        condominio_id bigint(20) NOT NULL, /* Nuevo campo para relacionar con la tabla de condominios */
        nombre varchar(255) NOT NULL,
        direccion varchar(255) NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (condominio_id) REFERENCES {$wpdb->prefix}condominios(id) ON DELETE CASCADE
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Registrar un nuevo domicilio.
// $data: array con los datos del domicilio, debe incluir el 'condominio_id'.
function registrar_domicilio($data) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    // Validar que el 'condominio_id' esté presente en los datos.
    if (!isset($data['condominio_id'])) {
        return 'Error: No se proporcionó un condominio válido.';
    }

    $result = $wpdb->insert($tabla_domicilios, array(
        'condominio_id' => intval($data['condominio_id']),
        'nombre' => sanitize_text_field($data['nombre']),
        'direccion' => sanitize_text_field($data['direccion'])
    ));

    return $result ? true : $wpdb->last_error;
}

// Actualizar un domicilio existente.
// $id: ID del domicilio a actualizar.
// $data: array con los datos actualizados, puede incluir 'nombre', 'direccion' y 'condominio_id'.
function actualizar_domicilio($id, $data) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $update_data = array();
    if (isset($data['nombre'])) {
        $update_data['nombre'] = sanitize_text_field($data['nombre']);
    }
    if (isset($data['direccion'])) {
        $update_data['direccion'] = sanitize_text_field($data['direccion']);
    }
    if (isset($data['condominio_id'])) {
        $update_data['condominio_id'] = intval($data['condominio_id']);
    }

    $result = $wpdb->update($tabla_domicilios, $update_data, array('id' => $id));

    return $result !== false ? true : $wpdb->last_error;
}

// Eliminar un domicilio.
// $id: ID del domicilio a eliminar.
function eliminar_domicilio($id) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';

    $result = $wpdb->delete($tabla_domicilios, array('id' => $id));

    return $result !== false ? true : $wpdb->last_error;
}

// Obtener todos los domicilios.
// Devuelve un array de objetos con la información de los domicilios.
function obtener_domicilios() {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    
    // Consulta extendida para incluir la información del condominio relacionado.
    $query = "
        SELECT d.*, c.nombre AS nombre_condominio
        FROM $tabla_domicilios d
        JOIN {$wpdb->prefix}condominios c ON d.condominio_id = c.id
    ";
    
    return $wpdb->get_results($query);
}

// Obtener un domicilio por su ID.
// $id: ID del domicilio.
// Devuelve un objeto con la información del domicilio.
function obtener_domicilio_por_id($id) {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    
    // Consulta extendida para obtener el nombre del condominio junto con el domicilio.
    $query = "
        SELECT d.*, c.nombre AS nombre_condominio
        FROM $tabla_domicilios d
        JOIN {$wpdb->prefix}condominios c ON d.condominio_id = c.id
        WHERE d.id = %d
    ";

    return $wpdb->get_row($wpdb->prepare($query, $id));
}

// Función para crear la tabla de condominios.
// Esta tabla será referenciada desde la tabla de domicilios.
function crear_tabla_condominios() {
    global $wpdb;
    $tabla_condominios = $wpdb->prefix . 'condominios';
    $charset_collate = $wpdb->get_charset_collate();

    // Crear la tabla de condominios.
    $sql = "CREATE TABLE $tabla_condominios (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        direccion text NOT NULL,
        superadmin_id bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Registrar un nuevo condominio.
// $data: array con los datos del condominio.
function registrar_condominio($data) {
    global $wpdb;
    $tabla_condominios = $wpdb->prefix . 'condominios';

    $result = $wpdb->insert($tabla_condominios, array(
        'nombre' => sanitize_text_field($data['nombre']),
        'direccion' => sanitize_textarea_field($data['direccion']),
        'superadmin_id' => intval($data['superadmin_id'])
    ));

    return $result ? true : $wpdb->last_error;
}

// Actualizar un condominio existente.
// $id: ID del condominio a actualizar.
// $data: array con los datos actualizados.
function actualizar_condominio($id, $data) {
    global $wpdb;
    $tabla_condominios = $wpdb->prefix . 'condominios';

    $update_data = array();
    if (isset($data['nombre'])) {
        $update_data['nombre'] = sanitize_text_field($data['nombre']);
    }
    if (isset($data['direccion'])) {
        $update_data['direccion'] = sanitize_textarea_field($data['direccion']);
    }

    $result = $wpdb->update($tabla_condominios, $update_data, array('id' => $id));

    return $result !== false ? true : $wpdb->last_error;
}

// Eliminar un condominio.
// $id: ID del condominio a eliminar.
function eliminar_condominio($id) {
    global $wpdb;
    $tabla_condominios = $wpdb->prefix . 'condominios';

    $result = $wpdb->delete($tabla_condominios, array('id' => $id));

    return $result !== false ? true : $wpdb->last_error;
}

// Obtener todos los condominios.
// Devuelve un array de objetos con la información de los condominios.
function obtener_condominios() {
    global $wpdb;
    $tabla_condominios = $wpdb->prefix . 'condominios';

    return $wpdb->get_results("SELECT * FROM $tabla_condominios");
}

// Obtener un condominio por su ID.
// $id: ID del condominio.
// Devuelve un objeto con la información del condominio.
function obtener_condominio_por_id($id) {
    global $wpdb;
    $tabla_condominios = $wpdb->prefix . 'condominios';

    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_condominios WHERE id = %d", $id));
}
?>
