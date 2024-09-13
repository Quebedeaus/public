<?php
// archivo: admin/vehiculos-admin-page.php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Asegúrate de que el usuario tenga permisos para gestionar vehículos
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

// Manejo del formulario para agregar o actualizar vehículos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar nonce para la seguridad
    check_admin_referer('save_vehicle');

    // Obtener los datos del formulario
    $vehicle_id = isset($_POST['vehicle_id']) ? intval($_POST['vehicle_id']) : 0;
    $foto = sanitize_text_field($_POST['foto']);
    $marca = sanitize_text_field($_POST['marca']);
    $modelo = sanitize_text_field($_POST['modelo']);
    $rfid = sanitize_text_field($_POST['rfid']);
    $propietario_id = isset($_POST['propietario_id']) ? intval($_POST['propietario_id']) : 0;
    $tipo = sanitize_text_field($_POST['tipo']); // 'residente' o 'visita'

    $datos_vehiculo = array(
        'foto' => $foto,
        'marca' => $marca,
        'modelo' => $modelo,
        'rfid' => $rfid,
        'propietario_id' => $propietario_id,
        'tipo' => $tipo,
    );

    if ($vehicle_id) {
        // Actualizar vehículo existente
        actualizar_vehiculo($vehicle_id, $datos_vehiculo);
    } else {
        // Agregar nuevo vehículo
        agregar_vehiculo($datos_vehiculo);
    }
    // Redirigir para evitar reenvío de formulario
    wp_redirect(admin_url('admin.php?page=vehiculos'));
    exit;
}

// Obtener el vehículo para editar si se pasa un ID
$vehicle_id = isset($_GET['vehicle_id']) ? intval($_GET['vehicle_id']) : 0;
$vehiculo = $vehicle_id ? obtener_vehiculo_por_id($vehicle_id) : (object)array(
    'foto' => '',
    'marca' => '',
    'modelo' => '',
    'rfid' => '',
    'propietario_id' => 0,
    'tipo' => 'residente', // Valor predeterminado
);

?>

<div class="wrap">
    <h1><?php echo $vehicle_id ? 'Editar Vehículo' : 'Agregar Nuevo Vehículo'; ?></h1>
    <form method="post" action="">
        <?php wp_nonce_field('save_vehicle'); ?>
        
        <input type="hidden" name="vehicle_id" value="<?php echo esc_attr($vehicle_id); ?>">

        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="foto">Foto del Vehículo</label></th>
                <td>
                    <?php if ($vehiculo->foto): ?>
                        <img src="<?php echo esc_url($vehiculo->foto); ?>" alt="Foto del Vehículo" style="max-width: 200px;">
                    <?php endif; ?>
                    <input type="url" name="foto" id="foto" value="<?php echo esc_attr($vehiculo->foto); ?>" placeholder="URL de la foto del vehículo">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="marca">Marca</label></th>
                <td>
                    <input type="text" name="marca" id="marca" value="<?php echo esc_attr($vehiculo->marca); ?>" class="regular-text">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="modelo">Modelo</label></th>
                <td>
                    <input type="text" name="modelo" id="modelo" value="<?php echo esc_attr($vehiculo->modelo); ?>" class="regular-text">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="rfid">ID del RFID</label></th>
                <td>
                    <input type="text" name="rfid" id="rfid" value="<?php echo esc_attr($vehiculo->rfid); ?>" class="regular-text">
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="propietario_id">Propietario</label></th>
                <td>
                    <select name="propietario_id" id="propietario_id">
                        <?php
                        // Obtener los propietarios (residentes y visitas)
                        global $wpdb;
                        $tabla_propietarios = $wpdb->prefix . 'propietarios';
                        $propietarios = $wpdb->get_results("SELECT id, nombre FROM $tabla_propietarios");
                        foreach ($propietarios as $propietario) {
                            echo '<option value="' . esc_attr($propietario->id) . '" ' . selected($vehiculo->propietario_id, $propietario->id, false) . '>' . esc_html($propietario->nombre) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="tipo">Tipo de Vehículo</label></th>
                <td>
                    <select name="tipo" id="tipo">
                        <option value="residente" <?php selected($vehiculo->tipo, 'residente'); ?>>Residente</option>
                        <option value="visita" <?php selected($vehiculo->tipo, 'visita'); ?>>Visita</option>
                    </select>
                </td>
            </tr>
            <!-- Agrega más campos según sea necesario -->
        </table>

        <?php submit_button($vehicle_id ? 'Actualizar Vehículo' : 'Agregar Vehículo'); ?>
    </form>
</div>

<?php
// Funciones de manejo de vehículos
function agregar_vehiculo($datos) {
    global $wpdb;
    $tabla_vehiculos = $wpdb->prefix . 'vehiculos';
    $wpdb->insert($tabla_vehiculos, $datos);
}

function obtener_vehiculo_por_id($id) {
    global $wpdb;
    $tabla_vehiculos = $wpdb->prefix . 'vehiculos';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $tabla_vehiculos WHERE id = %d", $id));
}

function actualizar_vehiculo($id, $datos) {
    global $wpdb;
    $tabla_vehiculos = $wpdb->prefix . 'vehiculos';
    $wpdb->update($tabla_vehiculos, $datos, array('id' => $id));
}
?>
