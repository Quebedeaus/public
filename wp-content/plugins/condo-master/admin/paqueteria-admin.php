<?php
// archivo: admin/paqueteria-admin.php

if (!defined('ABSPATH')) {
    exit; // Evitar acceso directo
}

// Agregar un hook para asegurar que se cargue solo en el área de administración de WordPress
add_action('admin_menu', 'registrar_pagina_paqueteria_admin');

function registrar_pagina_paqueteria_admin() {
    // Asegúrate de que el usuario tiene permisos para gestionar opciones
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes permisos suficientes para acceder a esta página.'));
    }

    // Agregar un menú de administración para la sección de paquetería
    add_submenu_page(
        'condo-master-admin',
      'Paquetería',
       'Paquetería',
        'manage_options', // Capacidad requerida
        'paqueteria-admin', // Slug de la página
        'mostrar_pagina_paqueteria_admin', // Función de contenido
        'dashicons-archive', // Icono del menú
        12 // Posición en el menú
    );
}

function mostrar_pagina_paqueteria_admin() {
    // Obtener si la foto es obligatoria desde las opciones de configuración
    $foto_obligatoria = get_option('paqueteria_foto_obligatoria', 'yes'); // 'yes' es el valor por defecto

    // Manejo del formulario para agregar o actualizar paquetes
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('save_package'); // Verificación del nonce

        // Validar y sanitizar los datos del formulario
        $package_id = isset($_POST['package_id']) ? intval($_POST['package_id']) : 0;
        $residente_id = isset($_POST['residente_id']) ? intval($_POST['residente_id']) : 0;
        $paquete = isset($_POST['paquete']) ? sanitize_text_field($_POST['paquete']) : '';
        $numero_rastreo = isset($_POST['numero_rastreo']) ? sanitize_text_field($_POST['numero_rastreo']) : ''; // Campo opcional
        $fecha_entrega = isset($_POST['fecha_entrega']) ? sanitize_text_field($_POST['fecha_entrega']) : '';
        $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
        $foto_paquete = isset($_FILES['foto_paquete']) ? $_FILES['foto_paquete'] : null;

        // Validaciones adicionales
        if (empty($residente_id) || !is_numeric($residente_id)) {
            wp_die(__('El ID del residente no es válido.'));
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_entrega)) {
            wp_die(__('La fecha de entrega debe estar en el formato YYYY-MM-DD.'));
        }

        // Verificar si la foto es obligatoria
        if ($foto_obligatoria === 'yes' && empty($foto_paquete['name'])) {
            wp_die(__('Es obligatoria la foto del paquete.'));
        }

        // Manejo de la foto del paquete
        if ($foto_paquete && !empty($foto_paquete['name'])) {
            // Subir la imagen
            $foto_subida = media_handle_upload('foto_paquete', 0);
            if (is_wp_error($foto_subida)) {
                wp_die(__('Hubo un error al subir la foto del paquete.'));
            }
            $foto_url = wp_get_attachment_url($foto_subida);
        } else {
            $foto_url = '';
        }

        // Preparar los datos del paquete
        $datos_paquete = array(
            'residente_id' => $residente_id,
            'paquete' => $paquete,
            'numero_rastreo' => $numero_rastreo, // Campo opcional
            'fecha_entrega' => $fecha_entrega,
            'status' => $status,
            'foto_url' => $foto_url,
        );

        // Insertar o actualizar el paquete en la base de datos
        if ($package_id) {
            $resultado = actualizar_paquete($package_id, $datos_paquete);
            if ($resultado === false) {
                wp_die(__('Hubo un error al actualizar el paquete.'));
            }
        } else {
            $resultado = agregar_paquete($datos_paquete);
            if ($resultado === false) {
                wp_die(__('Hubo un error al agregar el paquete.'));
            }
        }

        // Redireccionar después de guardar
        wp_redirect(admin_url('admin.php?page=paqueteria-admin'));
        exit;
    }

    // Obtener el paquete para editar si se pasa un ID
    $package_id = isset($_GET['package_id']) ? intval($_GET['package_id']) : 0;
    $paquete = $package_id ? obtener_paquete_por_id($package_id) : (object)array(
        'residente_id' => '',
        'paquete' => '',
        'numero_rastreo' => '',
        'fecha_entrega' => '',
        'status' => '',
        'foto_url' => '',
    );

    ?>
    <div class="wrap">
        <h1><?php echo $package_id ? 'Editar Paquete' : 'Agregar Nuevo Paquete'; ?></h1>
        <form method="post" enctype="multipart/form-data" action="">
            <?php wp_nonce_field('save_package'); ?>

            <input type="hidden" name="package_id" value="<?php echo esc_attr($package_id); ?>">

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="residente_id">Residente</label></th>
                    <td>
                        <?php
                        $residents = get_users(array('role' => 'residente'));
                        ?>
                        <select name="residente_id" id="residente_id">
                            <?php if (!empty($residents)): ?>
                                <?php foreach ($residents as $resident): ?>
                                    <option value="<?php echo esc_attr($resident->ID); ?>" <?php selected($paquete->residente_id, $resident->ID); ?>>
                                        <?php echo esc_html($resident->display_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value=""><?php echo __('No hay residentes disponibles.'); ?></option>
                            <?php endif; ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="paquete">Paquete</label></th>
                    <td>
                        <input type="text" name="paquete" id="paquete" value="<?php echo esc_attr($paquete->paquete); ?>" class="regular-text">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="numero_rastreo">Número de Rastreo (opcional)</label></th>
                    <td>
                        <input type="text" name="numero_rastreo" id="numero_rastreo" value="<?php echo esc_attr($paquete->numero_rastreo); ?>" class="regular-text">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="fecha_entrega">Fecha de Entrega</label></th>
                    <td>
                        <input type="date" name="fecha_entrega" id="fecha_entrega" value="<?php echo esc_attr($paquete->fecha_entrega); ?>">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="status">Estado</label></th>
                    <td>
                        <input type="text" name="status" id="status" value="<?php echo esc_attr($paquete->status); ?>" class="regular-text">
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="foto_paquete">Foto del Paquete</label></th>
                    <td>
                        <input type="file" name="foto_paquete" id="foto_paquete">
                        <?php if ($paquete->foto_url): ?>
                            <img src="<?php echo esc_url($paquete->foto_url); ?>" alt="Foto del paquete" style="max-width: 150px;">
                        <?php endif; ?>
                    </td>
                </tr>
            </table>

            <?php submit_button($package_id ? 'Actualizar Paquete' : 'Agregar Paquete'); ?>
        </form>
    </div>
    <?php
}
?>
