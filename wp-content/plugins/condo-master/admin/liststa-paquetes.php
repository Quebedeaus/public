<?php
// archivo: admin/lista-paquetes.php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Asegurarse de que el usuario tiene permisos para gestionar paquetes
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
}

global $wpdb;
$tabla_paquetes = $wpdb->prefix . 'packages';

// Obtener todos los paquetes
$paquetes = $wpdb->get_results("SELECT * FROM $tabla_paquetes");

?>

<div class="wrap">
    <h1>Listado de Paquetes</h1>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Residente</th>
                <th>Paquete</th>
                <th>Fecha de Entrega</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($paquetes): ?>
                <?php foreach ($paquetes as $paquete): ?>
                    <?php
                    $residente = get_user_by('ID', $paquete->residente_id);
                    ?>
                    <tr>
                        <td><?php echo esc_html($paquete->id); ?></td>
                        <td><?php echo esc_html($residente ? $residente->display_name : 'Desconocido'); ?></td>
                        <td><?php echo esc_html($paquete->paquete); ?></td>
                        <td><?php echo esc_html($paquete->fecha_entrega); ?></td>
                        <td><?php echo esc_html($paquete->status); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=paqueteria-admin&package_id=' . $paquete->id); ?>" class="button">Editar</a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=paqueteria-admin&action=delete&package_id=' . $paquete->id), 'delete_package'); ?>" class="button delete">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No hay paquetes registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Función para eliminar paquetes
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['package_id'])) {
    check_admin_referer('delete_package');
    $package_id = intval($_GET['package_id']);
    
    // Eliminar paquete
    $resultado = $wpdb->delete($tabla_paquetes, array('id' => $package_id));

    if ($resultado) {
        wp_redirect(admin_url('admin.php?page=lista-paquetes&message=deleted'));
        exit;
    } else {
        wp_die(__('Error al eliminar el paquete.'));
    }
}
?>
