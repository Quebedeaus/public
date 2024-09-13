<?php

// Función para mostrar el formulario de registro manual de usuarios
function custom_user_registration_form() {
    // Obtener la configuración de domicilios
    $config = get_option('domicilios_config', array());
    $tipo = isset($config['tipo']) ? $config['tipo'] : '';

    ?>
    <div class="wrap">
        <h1>Registro de Usuario</h1>
        <form method="post" action="options.php">
            <?php settings_fields('custom_user_registration'); ?>
            <?php do_settings_sections('custom_user_registration'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Nombre</th>
                    <td><input type="text" name="user_name" placeholder="Nombre" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Email</th>
                    <td><input type="email" name="user_email" placeholder="Email" required /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Teléfono</th>
                    <td><input type="text" name="user_phone" placeholder="Teléfono" /></td>
                </tr>

                <!-- Mostrar campos de dirección adicionales basados en la configuración -->
                <?php if ($tipo === 'residencial'): ?>
                    <?php if (isset($config['edificio']) && $config['edificio']): ?>
                        <tr valign="top">
                            <th scope="row">Edificio</th>
                            <td><input type="text" name="user_edificio" placeholder="Edificio" /></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($config['piso']) && $config['piso']): ?>
                        <tr valign="top">
                            <th scope="row">Piso</th>
                            <td><input type="text" name="user_piso" placeholder="Piso" /></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($config['torre']) && $config['torre']): ?>
                        <tr valign="top">
                            <th scope="row">Torre</th>
                            <td><input type="text" name="user_torre" placeholder="Torre" /></td>
                        </tr>
                    <?php endif; ?>
                <?php elseif ($tipo === 'industrial'): ?>
                    <?php if (isset($config['bodega']) && $config['bodega']): ?>
                        <tr valign="top">
                            <th scope="row">Bodega</th>
                            <td><input type="text" name="user_bodega" placeholder="Bodega" /></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($config['nave']) && $config['nave']): ?>
                        <tr valign="top">
                            <th scope="row">Nave</th>
                            <td><input type="text" name="user_nave" placeholder="Nave" /></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($config['anden']) && $config['anden']): ?>
                        <tr valign="top">
                            <th scope="row">Andén</th>
                            <td><input type="text" name="user_andén" placeholder="Andén" /></td>
                        </tr>
                    <?php endif; ?>
                <?php elseif ($tipo === 'comercial'): ?>
                    <?php if (isset($config['local']) && $config['local']): ?>
                        <tr valign="top">
                            <th scope="row">Local</th>
                            <td><input type="text" name="user_local" placeholder="Local" /></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (isset($config['oficina']) && $config['oficina']): ?>
                        <tr valign="top">
                            <th scope="row">Oficina</th>
                            <td><input type="text" name="user_oficina" placeholder="Oficina" /></td>
                        </tr>
                    <?php endif; ?>
                <?php endif; ?>

            </table>
            <?php submit_button('Registrar Usuario'); ?>
        </form>
    </div>
    <?php
}

function save_custom_user_data($user_id) {
    // Verificar que se está realizando una actualización de usuario
    if (isset($_POST['user_name'])) {
        update_user_meta($user_id, 'user_name', sanitize_text_field($_POST['user_name']));
    }
    if (isset($_POST['user_email'])) {
        update_user_meta($user_id, 'user_email', sanitize_email($_POST['user_email']));
    }
    if (isset($_POST['user_phone'])) {
        update_user_meta($user_id, 'user_phone', sanitize_text_field($_POST['user_phone']));
    }
    // Guardar campos de dirección adicionales basados en la configuración
    $config = get_option('domicilios_config', array());
    $tipo = isset($config['tipo']) ? $config['tipo'] : '';

    if ($tipo === 'residencial') {
        if (isset($_POST['user_edificio'])) {
            update_user_meta($user_id, 'user_edificio', sanitize_text_field($_POST['user_edificio']));
        }
        if (isset($_POST['user_piso'])) {
            update_user_meta($user_id, 'user_piso', sanitize_text_field($_POST['user_piso']));
        }
        if (isset($_POST['user_torre'])) {
            update_user_meta($user_id, 'user_torre', sanitize_text_field($_POST['user_torre']));
        }
    } elseif ($tipo === 'industrial') {
        if (isset($_POST['user_bodega'])) {
            update_user_meta($user_id, 'user_bodega', sanitize_text_field($_POST['user_bodega']));
        }
        if (isset($_POST['user_nave'])) {
            update_user_meta($user_id, 'user_nave', sanitize_text_field($_POST['user_nave']));
        }
        if (isset($_POST['user_andén'])) {
            update_user_meta($user_id, 'user_andén', sanitize_text_field($_POST['user_andén']));
        }
    } elseif ($tipo === 'comercial') {
        if (isset($_POST['user_local'])) {
            update_user_meta($user_id, 'user_local', sanitize_text_field($_POST['user_local']));
        }
        if (isset($_POST['user_oficina'])) {
            update_user_meta($user_id, 'user_oficina', sanitize_text_field($_POST['user_oficina']));
        }
    }
}
add_action('user_register', 'save_custom_user_data');

