<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Incluir funciones relacionadas con domicilios
require_once plugin_dir_path(__FILE__) . '../includes/domicilios-functions.php';

// Crear la tabla de domicilios si no existe
function crear_tabla_domicilios() {
    global $wpdb;
    $tabla_domicilios = $wpdb->prefix . 'domicilios';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_domicilios (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        direccion text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Función para mostrar la página de administración de domicilios
function domicilios_admin_page() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes permisos para acceder a esta página.'));
    }

    // Manejar acciones
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $domicilio_id = isset($_GET['domicilio_id']) ? intval($_GET['domicilio_id']) : 0;

    if ($action === 'edit' && $domicilio_id > 0) {
        display_edit_domicilio_form($domicilio_id);
        return;
    } elseif ($action === 'delete' && $domicilio_id > 0) {
        handle_delete_domicilio($domicilio_id);
    } elseif ($action === 'add') {
        display_add_domicilio_form();
        return;
    }

    ?>
    <div class="wrap">
        <h1>Administración de Domicilios</h1>
        <p>Desde aquí puedes gestionar los domicilios y su información.</p>
        <?php
        // Configuración de la paginación
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        // Obtener domicilios con paginación
        $domicilios = obtener_domicilios($per_page, $offset);
        $total_domicilios = contar_domicilios();

        if (!is_wp_error($domicilios) && !empty($domicilios)): ?>
            <table class="widefat fixed">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($domicilios as $domicilio): ?>
                        <tr>
                            <td><?php echo esc_html($domicilio->id); ?></td>
                            <td><?php echo esc_html($domicilio->nombre); ?></td>
                            <td><?php echo esc_html($domicilio->direccion); ?></td>
                            <td>
                                <?php
                                $edit_url = wp_nonce_url(add_query_arg(array('action' => 'edit', 'domicilio_id' => $domicilio->id), menu_page_url('domicilios-admin-page', false)), 'edit_domicilio_' . $domicilio->id);
                                $delete_url = wp_nonce_url(add_query_arg(array('action' => 'delete', 'domicilio_id' => $domicilio->id), menu_page_url('domicilios-admin-page', false)), 'delete_domicilio_' . $domicilio->id);
                                ?>
                                <a href="<?php echo esc_url($edit_url); ?>">Editar</a> |
                                <a href="<?php echo esc_url($delete_url); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este domicilio?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
            // Mostrar paginación
            $total_pages = ceil($total_domicilios / $per_page);
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
            <p>No hay domicilios disponibles.</p>
        <?php endif; ?>
        <a href="<?php echo esc_url(add_query_arg('action', 'add', menu_page_url('domicilios-admin-page', false))); ?>" class="button button-primary">Añadir Nuevo Domicilio</a>
    </div>
    <?php
}

function display_edit_domicilio_form($domicilio_id) {
    $domicilio = obtener_domicilio_por_id($domicilio_id);
    if (is_wp_error($domicilio)) {
        wp_die($domicilio->get_error_message());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_domicilio'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'update_domicilio_' . $domicilio_id)) {
            wp_die('Acción no autorizada');
        }

        $updated_data = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'direccion' => sanitize_textarea_field($_POST['direccion'])
        );

        $result = actualizar_domicilio($domicilio_id, $updated_data);
        if (!is_wp_error($result)) {
            echo '<div class="notice notice-success"><p>Domicilio actualizado con éxito.</p></div>';
            $domicilio = obtener_domicilio_por_id($domicilio_id);
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Editar Domicilio</h1>
        <form method="post" action="">
            <?php wp_nonce_field('update_domicilio_' . $domicilio_id); ?>
            <table class="form-table">
                <tr>
                    <th><label for="nombre">Nombre</label></th>
                    <td><input type="text" name="nombre" id="nombre" value="<?php echo esc_attr($domicilio->nombre); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="direccion">Dirección</label></th>
                    <td><textarea name="direccion" id="direccion" rows="5" required><?php echo esc_textarea($domicilio->direccion); ?></textarea></td>
                </tr>
            </table>
            <input type="submit" name="update_domicilio" class="button button-primary" value="Actualizar Domicilio">
        </form>
    </div>
    <?php
}

function display_add_domicilio_form() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_domicilio'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'add_domicilio')) {
            wp_die('Acción no autorizada');
        }

        $new_domicilio = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'direccion' => sanitize_textarea_field($_POST['direccion'])
        );

        $result = registrar_domicilio($new_domicilio);
        if (!is_wp_error($result)) {
            echo '<div class="notice notice-success"><p>Domicilio añadido con éxito.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Añadir Nuevo Domicilio</h1>
        <form method="post" action="">
            <?php wp_nonce_field('add_domicilio'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="nombre">Nombre</label></th>
                    <td><input type="text" name="nombre" id="nombre" required></td>
                </tr>
                <tr>
                    <th><label for="direccion">Dirección</label></th>
                    <td><textarea name="direccion" id="direccion" rows="5" required></textarea></td>
                </tr>
            </table>
            <input type="submit" name="add_domicilio" class="button button-primary" value="Añadir Domicilio">
        </form>
    </div>
    <?php
}

function handle_delete_domicilio($domicilio_id) {
    if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_domicilio_' . $domicilio_id)) {
        wp_die('Acción no autorizada');
    }

    $result = eliminar_domicilio($domicilio_id);
    if (!is_wp_error($result)) {
        wp_redirect(menu_page_url('domicilios-admin-page', false));
        exit;
    } else {
        wp_die($result->get_error_message());
    }
}

// Añadir página de administración al menú
function domicilios_admin_menu() {
    add_menu_page(
        'Administración de Domicilios',
        'Domicilios',
        'manage_options',
        'domicilios-admin-page',
        'domicilios_admin_page',
        'dashicons-admin-home'
    );
}

add_action('admin_menu', 'domicilios_admin_menu');
add_action('admin_init', 'crear_tabla_domicilios');
?>
