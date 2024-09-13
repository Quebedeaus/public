<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Crear la página de administración de diligencias
function crear_pagina_administracion_diligencias() {
    add_menu_page(
        'Diligencias', // Título de la página
        'Diligencias', // Título del menú
        'manage_options', // Capacidad
        'diligencias', // Slug del menú
        'mostrar_pagina_administracion_diligencias', // Función de callback
        'dashicons-clipboard', // Icono del menú
        6 // Posición en el menú
    );
}
add_action('admin_menu', 'crear_pagina_administracion_diligencias');

// Mostrar la página de administración de diligencias
function mostrar_pagina_administracion_diligencias() {
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Diligencias</h1>
        <a href="?page=diligencias&action=agregar" class="page-title-action">Agregar Nueva</a>

        <?php
        // Mostrar mensajes de error o éxito
        if (isset($_GET['mensaje'])) {
            $mensaje = sanitize_text_field($_GET['mensaje']);
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($mensaje) . '</p></div>';
        }

        // Obtener diligencias
        $diligencias = obtener_diligencias(array('number' => 10));

        if ($diligencias) {
            ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($diligencias as $diligencia) { ?>
                    <tr>
                        <td><?php echo esc_html($diligencia->id); ?></td>
                        <td><?php echo esc_html($diligencia->nombre); ?></td>
                        <td><?php echo esc_html($diligencia->descripcion); ?></td>
                        <td><?php echo esc_html($diligencia->fecha); ?></td>
                        <td>
                            <a href="?page=diligencias&action=editar&id=<?php echo esc_attr($diligencia->id); ?>">Editar</a> |
                            <a href="?page=diligencias&action=eliminar&id=<?php echo esc_attr($diligencia->id); ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta diligencia?');">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <?php
        } else {
            echo '<p>No hay diligencias disponibles.</p>';
        }
        ?>

        <?php
        // Mostrar formulario de agregar o editar
        if (isset($_GET['action']) && ($_GET['action'] == 'agregar' || $_GET['action'] == 'editar')) {
            $diligencia_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $diligencia = $diligencia_id ? obtener_diligencia_por_id($diligencia_id) : null;

            ?>
            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th><label for="nombre">Nombre</label></th>
                        <td><input type="text" id="nombre" name="nombre" value="<?php echo esc_attr($diligencia ? $diligencia->nombre : ''); ?>" required></td>
                    </tr>
                    <tr>
                        <th><label for="descripcion">Descripción</label></th>
                        <td><textarea id="descripcion" name="descripcion" required><?php echo esc_textarea($diligencia ? $diligencia->descripcion : ''); ?></textarea></td>
                    </tr>
                </table>
                <input type="hidden" name="action" value="<?php echo esc_attr($_GET['action']); ?>">
                <input type="hidden" name="diligencia_id" value="<?php echo esc_attr($diligencia_id); ?>">
                <?php wp_nonce_field('guardar_diligencia', 'diligencia_nonce'); ?>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php echo ($_GET['action'] == 'editar') ? 'Actualizar' : 'Agregar'; ?> Diligencia">
                </p>
            </form>
            <?php
        }

        // Procesar formulario de agregar o editar
        if (isset($_POST['action'])) {
            check_admin_referer('guardar_diligencia', 'diligencia_nonce');

            $nombre = sanitize_text_field($_POST['nombre']);
            $descripcion = sanitize_textarea_field($_POST['descripcion']);
            $diligencia_id = intval($_POST['diligencia_id']);

            if ($_POST['action'] == 'agregar') {
                $resultado = registrar_diligencia(array('nombre' => $nombre, 'descripcion' => $descripcion));
                if (is_wp_error($resultado)) {
                    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($resultado->get_error_message()) . '</p></div>';
                } else {
                    wp_redirect(add_query_arg('mensaje', 'Diligencia agregada con éxito', admin_url('admin.php?page=diligencias')));
                    exit;
                }
            } elseif ($_POST['action'] == 'editar') {
                $resultado = actualizar_diligencia($diligencia_id, array('nombre' => $nombre, 'descripcion' => $descripcion));
                if (is_wp_error($resultado)) {
                    echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($resultado->get_error_message()) . '</p></div>';
                } else {
                    wp_redirect(add_query_arg('mensaje', 'Diligencia actualizada con éxito', admin_url('admin.php?page=diligencias')));
                    exit;
                }
            }
        }

        // Eliminar diligencia
        if (isset($_GET['action']) && $_GET['action'] == 'eliminar' && isset($_GET['id'])) {
            $diligencia_id = intval($_GET['id']);
            $resultado = eliminar_diligencia($diligencia_id);
            if (is_wp_error($resultado)) {
                echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($resultado->get_error_message()) . '</p></div>';
            } else {
                wp_redirect(add_query_arg('mensaje', 'Diligencia eliminada con éxito', admin_url('admin.php?page=diligencias')));
                exit;
            }
        }
        ?>
    </div>
    <?php
}
?>
