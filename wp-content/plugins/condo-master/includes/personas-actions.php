<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Manejar la adición de una nueva persona
function handle_add_person() {
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
}

// Manejar la actualización de una persona
function handle_update_person($person_id) {
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
        } else {
            echo '<div class="notice notice-error"><p>' . $result->get_error_message() . '</p></div>';
        }
    }
}

// Manejar la eliminación de una persona
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

// Llamar a las funciones de manejo según la acción
add_action('admin_init', function() {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    $person_id = isset($_GET['person_id']) ? intval($_GET['person_id']) : 0;

    if ($action === 'add') {
        handle_add_person();
    } elseif ($action === 'edit' && $person_id > 0) {
        handle_update_person($person_id);
    } elseif ($action === 'delete' && $person_id > 0) {
        handle_delete_person($person_id);
    }
});
?>
