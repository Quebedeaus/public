<?php
// Mostrar el formulario de subida de CSV
function custom_csv_upload_form() {
    ?>
    <div class="wrap">
        <h1>Subir CSV de Usuarios</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file" />
            <input type="hidden" name="action" value="upload_csv_file" />
            <?php submit_button('Subir CSV'); ?>
        </form>
    </div>
    <?php
}

// Procesar el archivo CSV subido
function handle_csv_upload() {
    if (isset($_POST['action']) && $_POST['action'] === 'upload_csv_file' && !empty($_FILES['csv_file']['tmp_name'])) {
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
add_action('admin_post_upload_csv_file', 'handle_csv_upload');

// Añadir la página de subida de CSV al menú de administración
function add_csv_upload_menu() {
    add_submenu_page('custom_user_registration', 'Subir CSV', 'Subir CSV', 'manage_options', 'upload_csv_file', 'custom_csv_upload_form');
}
add_action('admin_menu', 'add_csv_upload_menu');
