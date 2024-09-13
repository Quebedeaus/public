<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Incluir funciones de personas
require_once plugin_dir_path(__FILE__) . '../includes/personas-functions.php';

// Crear la tabla de personas si no existe
function crear_tabla_personas() {
    global $wpdb;
    $tabla_personas = $wpdb->prefix . 'personas';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_personas (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        nombre varchar(255) NOT NULL,
        telefono varchar(20) DEFAULT '' NOT NULL,
        domicilio_id bigint(20) NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (domicilio_id) REFERENCES {$wpdb->prefix}domicilios(id) ON DELETE CASCADE
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Función para mostrar la página de administración de personas
function personas_admin_page() {
    // Verificar permisos
    if (!current_user_can('manage_options')) {
        wp_die(__('No tienes permisos para acceder a esta página.'));
    }

    // Manejar acciones
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $person_id = isset($_GET['person_id']) ? intval($_GET['person_id']) : 0;

    if ($action === 'edit' && $person_id > 0) {
        display_edit_person_form($person_id);
        return;
    } elseif ($action === 'delete' && $person_id > 0) {
        handle_delete_person($person_id);
    } elseif ($action === 'add') {
        display_add_person_form();
        return;
    }

    ?>
    <div class="wrap">
        <h1>Administración de Personas</h1>
        <p>Desde aquí puedes gestionar las personas y su información.</p>
        <?php
        // Configuración de la paginación
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;

        // Obtener personas con paginación
        $personas = obtener_personas($per_page, $offset);
        $total_personas = contar_personas();

        if (!is_wp_error($personas) && !empty($personas)): ?>
            <table class="widefat fixed">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Domicilio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($personas as $persona): ?>
                        <tr>
                            <td><?php echo esc_html($persona->id); ?></td>
                            <td><?php echo esc_html($persona->nombre); ?></td>
                            <td><?php echo esc_html($persona->telefono); ?></td>
                            <td><?php echo esc_html(get_domicilio_name($persona->domicilio_id)); ?></td>
                            <td>
                                <?php
                                $edit_url = wp_nonce_url(add_query_arg(array('action' => 'edit', 'person_id' => $persona->id), menu_page_url('personas-admin', false)), 'edit_person_' . $persona->id);
                                $delete_url = wp_nonce_url(add_query_arg(array('action' => 'delete', 'person_id' => $persona->id), menu_page_url('personas-admin', false)), 'delete_person_' . $persona->id);
                                ?>
                                <a href="<?php echo esc_url($edit_url); ?>">Editar</a> |
                                <a href="<?php echo esc_url($delete_url); ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar esta persona?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php
            // Mostrar paginación
            $total_pages = ceil($total_personas / $per_page);
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
            <p>No hay personas disponibles.</p>
        <?php endif; ?>
        <a href="<?php echo esc_url(add_query_arg('action', 'add', menu_page_url('personas-admin', false))); ?>" class="button button-primary">Añadir Nueva Persona</a>
    </div>
    <?php
}

function display_edit_person_form($person_id) {
    $persona = obtener_persona_por_id($person_id);
    if (is_wp_error($persona)) {
        wp_die($persona->get_error_message());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_person'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'update_person_' . $person_id)) {
            wp_die('Acción no autorizada');
        }

        $updated_data = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'telefono' => sanitize_text_field($_POST['telefono']),
            'domicilio_id' => intval($_POST['domicilio_id'])
        );

        $result = actualizar_persona($person_id, $updated_data);
        if (!is_wp_error($result)) {
            echo '<div class="notice notice-success"><p>Persona actualizada con éxito.</p></div>';
            $persona = obtener_persona_por_id($person_id);
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Editar Persona</h1>
        <form method="post" action="">
            <?php wp_nonce_field('update_person_' . $person_id); ?>
            <table class="form-table">
                <tr>
                    <th><label for="nombre">Nombre</label></th>
                    <td><input type="text" name="nombre" id="nombre" value="<?php echo esc_attr($persona->nombre); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="telefono">Teléfono</label></th>
                    <td><input type="text" name="telefono" id="telefono" value="<?php echo esc_attr($persona->telefono); ?>" required></td>
                </tr>
                <tr>
                    <th><label for="domicilio_id">Domicilio</label></th>
                    <td>
                        <select name="domicilio_id" id="domicilio_id" required>
                            <?php
                            $domicilios = obtener_domicilios();
                            foreach ($domicilios as $domicilio) {
                                echo '<option value="' . esc_attr($domicilio->id) . '" ' . selected($domicilio->id, $persona->domicilio_id, false) . '>' . esc_html($domicilio->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
            <input type="submit" name="update_person" class="button button-primary" value="Actualizar Persona">
        </form>
    </div>
    <?php
}

function display_add_person_form() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_person'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'add_person')) {
            wp_die('Acción no autorizada');
        }

        $new_person = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'telefono' => sanitize_text_field($_POST['telefono']),
            'domicilio_id' => intval($_POST['domicilio_id'])
        );

        $result = registrar_persona($new_person);
        if (!is_wp_error($result)) {
            echo '<div class="notice notice-success"><p>Persona añadida con éxito.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }

    ?>
    <div class="wrap">
        <h1>Añadir Nueva Persona</h1>
        <form method="post" action="">
            <?php wp_nonce_field('add_person'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="nombre">Nombre</label></th>
                    <td><input type="text" name="nombre" id="nombre" required></td>
                </tr>
                <tr>
                    <th><label for="telefono">Teléfono</label></th>
                    <td><input type="text" name="telefono" id="telefono" required></td>
                </tr>
                <tr>
                    <th><label for="domicilio_id">Domicilio</label></th>
                    <td>
                        <select name="domicilio_id" id="domicilio_id" required>
                            <?php
                            $domicilios = obtener_domicilios();
                            foreach ($domicilios as $domicilio) {
                                echo '<option value="' . esc_attr($domicilio->id) . '">' . esc_html($domicilio->nombre) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
            </table>
            <input type="submit" name="add_person" class="button button-primary" value="Añadir Persona">
        </form>
    </div>
    <?php
}

function handle_delete_person($person_id) {
    if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_person_' . $person_id)) {
        wp_die('Acción no autorizada');
    }

    $result = eliminar_persona($person_id);
    if (!is_wp_error($result)) {
        wp_redirect(menu_page_url('personas-admin', false));
        exit;
    } else {
        wp_die($result->get_error_message());
    }
}

// Hook para crear la tabla al activar el plugin
register_activation_hook(__FILE__, 'crear_tabla_personas');

// Agregar menú de administración
add_action('admin_menu', function() {
    add_menu_page(
        'Administración de Personas',
        'Personas',
        'manage_options',
        'personas-admin',
        'personas_admin_page'
    );
});
?>
