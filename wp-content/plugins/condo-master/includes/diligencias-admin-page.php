<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Mostrar la página de administración de diligencias
function diligencias_admin_page() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes permisos para acceder a esta página.'));
    }

    // Manejar acciones
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $diligencia_id = isset($_GET['diligencia_id']) ? intval($_GET['diligencia_id']) : 0;

    if ($action === 'edit' && $diligencia_id > 0) {
        display_edit_diligencia_form($diligencia_id);
        return;
    } elseif ($action === 'delete' && $diligencia_id > 0) {
        handle_delete_diligencia($diligencia_id);
    } elseif ($action === 'add') {
        display_add_diligencia_form();
        return;
    }

    ?>
    <div class="wrap">
        <h1>Administración de Diligencias</h1>
        <p>Desde aquí puedes gestionar las diligencias.</p>
        <?php
        // Configuración de la paginación
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        // Obtener diligencias con paginación
        $diligencias = obtener_diligencias(array(
            'number' => $per_page,
            'offset' => $offset
        ));
        $total_diligencias = contar_diligencias();

        if (!is_wp_error($diligencias) && !empty($diligencias)): ?>
            <table class="widefat fixed">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diligencias as $diligencia): ?>
                        <tr>
                            <td><?php echo esc_html($diligencia->id); ?></td>
                            <td><?php echo esc_html($diligencia->nombre); ?></td>
                            <td><?php echo esc_html($diligencia->descripcion); ?></td>
                            <td><?php echo esc_html($diligencia->fecha); ?></td>
                            <td>
                                <?php
                                $edit_url = wp_nonce_url(add_query_arg(array('action' => 'edit', 'diligencia_id' => $diligencia->id), menu_page_url('diligencias-admin', false)), 'edit_diligencia_' . $diligencia->id);
                                $delete_url = wp_nonce_url(add_query_arg(array('action' => 'delete', 'diligencia_id' => $diligencia->id), menu_page_url('diligencias-admin', false)), 'delete_diligencia_' . $diligencia->id);
                                ?>
                                <a href="<?php echo esc_url($edit_url); ?>">Editar</a> |
                                <a href="<?php echo esc_url($delete_url); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar esta diligencia?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
            // Mostrar paginación
            $total_pages = ceil($total_diligencias / $per_page);
            echo paginate_links(array(
                'base' => add_query_arg('paged', '%#%'),
                'format' => '',
                'prev_text' => __('&laquo;'),
                'next_text' => __('&raquo;'),
                'total' => $total_pages,
                'current' => $current_page
            ));
            ?>
        <?php else: ?>
            <p>No hay diligencias disponibles.</p>
        <?php endif; ?>
        <a href="<?php echo esc_url(add_query_arg('action', 'add', menu_page_url('diligencias-admin', false))); ?>" class="button button-primary">Añadir Nueva Diligencia</a>
    </div>
    <?php
}

function display_edit_diligencia_form($diligencia_id) {
    $diligencia = obtener_diligencia_por_id($diligencia_id);
    if (is_wp_error($diligencia)) {
        wp_die($diligencia->get_error_message());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_diligencia'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'update_diligencia_' . $diligencia_id)) {
            wp_die('Acción no autorizada');
        }

        $updated_data = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'descripcion' => sanitize_textarea_field($_POST['descripcion'])
        );

        $result = actualizar_diligencia($diligencia_id, $updated_data);
        if (!is_wp_error($result)) {
            echo '<div class="notice notice-success"><p>Diligencia actualizada con éxito.</p></div>';
            $diligencia = obtener_diligencia_por_id($diligencia_id);
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Editar Diligencia</h1>
        <form method="post" action="">
            <?php wp_nonce_field('update_diligencia_' . $diligencia_id); ?>
            <table class="form-table">
                <tr>
                    <th><label for="nombre">Nombre</label></th>
                    <td><input type="text" name="nombre" id="nombre" value="<?php echo esc_attr($diligencia->nombre); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="descripcion">Descripción</label></th>
                    <td><textarea name="descripcion" id="descripcion" required><?php echo esc_textarea($diligencia->descripcion); ?></textarea></td>
                </tr>
            </table>
            <input type="submit" name="update_diligencia" class="button button-primary" value="Actualizar Diligencia">
        </form>
    </div>
    <?php
}

function display_add_diligencia_form() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_diligencia'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'add_diligencia')) {
            wp_die('Acción no autorizada');
        }

        $new_diligencia = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'descripcion' => sanitize_textarea_field($_POST['descripcion'])
        );

        $result = registrar_diligencia($new_diligencia);
        if (!is_wp_error($result)) {
            echo '<div class="notice notice-success"><p>Diligencia añadida con éxito.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Añadir Nueva Diligencia</h1>
        <form method="post" action="">
            <?php wp_nonce_field('add_diligencia'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="nombre">Nombre</label></th>
                    <td><input type="text" name="nombre" id="nombre" required></td>
                </tr>
                <tr>
                    <th><label for="descripcion">Descripción</label></th>
                    <td><textarea name="descripcion" id="descripcion" required></textarea></td>
                </tr>
            </table>
            <input type="submit" name="add_diligencia" class="button button-primary" value="Añadir Diligencia">
        </form>
    </div>
    <?php
}

function handle_delete_diligencia($diligencia_id) {
    if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_diligencia_' . $diligencia_id)) {
        wp_die('Acción no autorizada');
    }

    $result = eliminar_diligencia($diligencia_id);
    if (!is_wp_error($result)) {
        wp_redirect(menu_page_url('diligencias-admin', false));
        exit;
    } else {
        wp_die($result->get_error_message());
    }
}

// Agregar menú de administración
add_action('admin_menu', function() {
    add_menu_page(
        'Administración de Diligencias',
        'Diligencias',
        'manage_options',
        'diligencias-admin',
        'diligencias_admin_page'
    );
});
?>
