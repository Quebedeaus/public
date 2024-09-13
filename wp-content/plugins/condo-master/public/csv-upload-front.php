<?php
// Mostrar el formulario de subida de CSV en el frontend
function custom_csv_upload_frontend_form() {
    if (is_user_logged_in() && current_user_can('manage_options')) { // Solo para administradores
        ?>
        <div class="csv-upload-form">
            <h2>Subir CSV de Usuarios</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="csv_file" />
                <input type="hidden" name="action" value="upload_csv_file_frontend" />
                <input type="submit" value="Subir CSV" />
            </form>
        </div>
        <?php
    } else {
        echo 'No tienes permiso para acceder a esta página.';
    }
}
add_shortcode('csv_upload_frontend', 'custom_csv_upload_frontend_form');

// Procesar el archivo CSV subido desde el frontend
function handle_csv_upload_frontend() {
    if (isset($_POST['action']) && $_POST['action'] === 'upload_csv_file_frontend' && !empty($_FILES['csv_file']['tmp_name'])) {
        if (($handle = fopen($_FILES['csv_file']['tmp_name'], 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $user_data = array(
                    'user_login' => sanitize_text_field($data[0]),
                    'user_email' => sanitize_email($data[1]),
                    'user_pass'  => wp_generate_password(), // Generar una contraseña aleatoria
                    'meta_input' => array(
                        'phone' => sanitize_text_field($data[2]),
                    ),
                );
                wp_insert_user($user_data);
            }
            fclose($handle);
        }
    }
}
add_action('wp', 'handle_csv_upload_frontend');
