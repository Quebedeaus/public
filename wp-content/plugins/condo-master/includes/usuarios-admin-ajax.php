<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Verificar el nonce de seguridad para AJAX
function verificar_nonce() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'usuarios_admin_nonce')) {
        wp_send_json_error('Nonce inválido');
    }
}

// Manejar la solicitud AJAX para agregar un usuario
function agregar_usuario() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $nombre = sanitize_text_field($_POST['nombre']);
    $email = sanitize_email($_POST['email']);
    $telefono = sanitize_text_field($_POST['telefono']);
    $rol = sanitize_text_field($_POST['rol']);

    // Validación del email
    if (!is_email($email)) {
        wp_send_json_error('Email inválido');
    }

    // Crear el usuario
    $user_id = wp_create_user($nombre, wp_generate_password(), $email);
    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_message());
    }

    // Asignar rol al usuario
    $user = new WP_User($user_id);
    $user->set_role($rol);

    // Guardar teléfono en meta
    update_user_meta($user_id, 'telefono', $telefono);

    wp_send_json_success('Usuario añadido con éxito');
}

// Manejar la solicitud AJAX para editar un usuario
function editar_usuario() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $user_id = intval($_POST['user_id']);
    $nombre = sanitize_text_field($_POST['nombre']);
    $email = sanitize_email($_POST['email']);
    $telefono = sanitize_text_field($_POST['telefono']);
    $rol = sanitize_text_field($_POST['rol']);

    // Validación del email
    if (!is_email($email)) {
        wp_send_json_error('Email inválido');
    }

    // Actualizar datos del usuario
    wp_update_user(array(
        'ID' => $user_id,
        'user_login' => $nombre,
        'user_email' => $email,
    ));

    // Actualizar rol del usuario
    $user = new WP_User($user_id);
    $user->set_role($rol);

    // Actualizar teléfono en meta
    update_user_meta($user_id, 'telefono', $telefono);

    wp_send_json_success('Usuario actualizado con éxito');
}

// Manejar la solicitud AJAX para eliminar un usuario
function eliminar_usuario() {
    verificar_nonce();

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Permiso denegado');
    }

    $user_id = intval($_POST['user_id']);

    // Eliminar el usuario
    require_once(ABSPATH . 'wp-admin/includes/user.php');
    $result = wp_delete_user($user_id);

    if ($result) {
        wp_send_json_success('Usuario eliminado con éxito');
    } else {
        wp_send_json_error('Error al eliminar el usuario');
    }
}

// Hook para manejar las solicitudes AJAX
add_action('wp_ajax_agregar_usuario', 'agregar_usuario');
add_action('wp_ajax_editar_usuario', 'editar_usuario');
add_action('wp_ajax_eliminar_usuario', 'eliminar_usuario');
?>
