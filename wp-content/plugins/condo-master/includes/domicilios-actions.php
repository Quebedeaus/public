<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Manejar la adición de un nuevo domicilio
function handle_add_domicilio() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_domicilio'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'add_domicilio')) {
            wp_die('Acción no autorizada');
        }

        $new_domicilio = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'direccion' => sanitize_textarea_field($_POST['direccion']),
            'telefono' => sanitize_text_field($_POST['telefono'])
        );

        $result = registrar_domicilio($new_domicilio);
        if (!is_wp_error($result)) {
            echo '<div class="notice notice-success"><p>Domicilio añadido con éxito.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }
}

// Manejar la actualización de un domicilio
function handle_update_domicilio($domicilio_id) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_domicilio'])) {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'update_domicilio_' . $domicilio_id)) {
            wp_die('Acción no autorizada');
        }

        $updated_data = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'direccion' => sanitize_textarea_field($_POST['direccion']),
            'telefono' => sanitize_text_field($_POST['telefono'])
        );

        $result = actualizar_domicilio($domicilio_id, $updated_data);
        if (!is_wp_error($result)) {
            echo '<div class="notice notice-success"><p>Domicilio actualizado con éxito.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }
}

// Manejar la eliminación de un domicilio
function handle_delete_domicilio($domicilio_id) {
    if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_domicilio_' . $domicilio_id)) {
        wp_die('Acción no autorizada');
    }

    $result = eliminar_domicilio($domicilio_id);
    if (!is_wp_error($result)) {
        wp_redirect(menu_page_url('domicilios-admin', false));
        exit;
    } else {
        wp_die($result->get_error_message());
    }
}

// Llamar a las funciones de manejo según la acción
add_action('admin_init', function() {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $domicilio_id = isset($_GET['domicilio_id']) ? intval($_GET['domicilio_id']) : 0;

    if ($action === 'add') {
        handle_add_domicilio();
    } elseif ($action === 'edit' && $domicilio_id > 0) {
        handle_update_domicilio($domicilio_id);
    } elseif ($action === 'delete' && $domicilio_id > 0) {
        handle_delete_domicilio($domicilio_id);
    }
});
?>
