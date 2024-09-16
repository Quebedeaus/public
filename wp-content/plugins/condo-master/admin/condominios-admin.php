<?php
// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

// Agregar menú para el creador de condominios en el dashboard del superadmin.
function condominios_admin_menu() {
    add_menu_page(
        'Gestión de Condominios',
        'Condominios',
        'manage_options',
        'condominios-admin',
        'condominios_admin_page',
        'dashicons-building',
        25
    );
}
add_action('admin_menu', 'condominios_admin_menu');

// Función para manejar la página del creador de condominios.
function condominios_admin_page() {
    ?>
    <div class="wrap">
        <h1>Crear un nuevo Condominio</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Nombre del Condominio</th>
                    <td><input type="text" name="condominio_nombre" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Dirección</th>
                    <td><textarea name="condominio_direccion" required></textarea></td>
                </tr>
                <tr valign="top">
                    <th scope="row">ID del Superadmin</th>
                    <td><input type="text" name="superadmin_id" value="<?php echo get_current_user_id(); ?>" readonly /></td>
                </tr>
            </table>
            <input type="submit" name="crear_condominio" class="button button-primary" value="Crear Condominio" />
        </form>
    </div>
    <?php

    // Guardar condominio si el formulario ha sido enviado.
    if (isset($_POST['crear_condominio'])) {
        $nombre = sanitize_text_field($_POST['condominio_nombre']);
        $direccion = sanitize_textarea_field($_POST['condominio_direccion']);
        $superadmin_id = sanitize_text_field($_POST['superadmin_id']);

        crear_condominio($nombre, $direccion, $superadmin_id);
    }
}

// Función para crear un nuevo condominio.
function crear_condominio($nombre, $direccion, $superadmin_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'condominios';

    $wpdb->insert($table_name, [
        'nombre' => $nombre,
        'direccion' => $direccion,
        'superadmin_id' => $superadmin_id,
    ]);

    echo "<div class='updated'><p>Condominio creado exitosamente.</p></div>";
}
?>
