<?php
// Mostrar el formulario de registro manual en el frontend
function custom_user_registration_frontend_form() {
    if (is_user_logged_in() && current_user_can('manage_options')) { // Solo para administradores
        ?>
        <div class="user-registration-form">
            <h2>Registro de Usuario</h2>
            <form method="post" action="">
                <input type="text" name="user_name" placeholder="Nombre" required />
                <input type="email" name="user_email" placeholder="Email" required />
                <input type="text" name="user_phone" placeholder="Teléfono" />
                <input type="hidden" name="action" value="custom_user_registration_frontend" />
                <input type="submit" value="Registrar Usuario" />
            </form>
        </div>
        <?php
    } else {
        echo 'No tienes permiso para acceder a esta página.';
    }
}
add_shortcode('user_registration_frontend', 'custom_user_registration_frontend_form');

// Guardar el usuario registrado manualmente desde el frontend
function handle_user_registration_frontend() {
    if (isset($_POST['action']) && $_POST['action'] === 'custom_user_registration_frontend') {
        $user_data = array(
            'user_login' => sanitize_text_field($_POST['user_name']),
            'user_email' => sanitize_email($_POST['user_email']),
            'user_pass'  => wp_generate_password(), // Generar una contraseña aleatoria
            'meta_input' => array(
                'phone' => sanitize_text_field($_POST['user_phone']),
            ),
        );
        wp_insert_user($user_data);
    }
}
add_action('wp', 'handle_user_registration_frontend');
