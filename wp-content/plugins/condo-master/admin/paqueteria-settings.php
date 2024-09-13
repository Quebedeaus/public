<?php
// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

// Página de Configuración del Módulo de Paquetería
function paqueteria_settings_page() {
    ?>
    <div class="wrap">
        <h1>Configuración del Módulo de Paquetería</h1>
        <?php if (isset($_GET['settings-updated'])) : ?>
            <div class="notice notice-success is-dismissible">
                <p>Configuración actualizada con éxito.</p>
            </div>
        <?php endif; ?>
        <form method="post" action="options.php">
            <?php
            settings_fields('paquete_module_settings');
            do_settings_sections('paquete_module_settings');
            ?>
            <input type="submit" name="submit" id="submit" class="button button-primary" value="Guardar Cambios">
        </form>
    </div>
    <?php
}

function paqueteria_settings_init() {
    // Registrar configuraciones.
    register_setting('paquete_module_settings', 'paquete_module_active');
    register_setting('paquete_module_settings', 'paquete_email_notifications');
    
    // Añadir secciones.
    add_settings_section(
        'paquete_module_section',
        'Configuraciones Generales',
        'paquete_module_section_callback',
        'paquete_module_settings'
    );
    
    // Añadir campos de configuración.
    add_settings_field(
        'paquete_module_active',
        'Activar Módulo',
        'paquete_module_active_render',
        'paquete_module_settings',
        'paquete_module_section'
    );
    
    add_settings_field(
        'paquete_email_notifications',
        'Notificaciones por Email',
        'paquete_email_notifications_render',
        'paquete_module_settings',
        'paquete_module_section'
    );
    
    add_settings_field(
        'paquete_sender_field_label',
        'Etiqueta del Campo del Remitente',
        'paquete_sender_field_label_render',
        'paquete_module_settings',
        'paquete_module_section'
    );
    
    add_settings_field(
        'paquete_description_field_label',
        'Etiqueta del Campo de Descripción',
        'paquete_description_field_label_render',
        'paquete_module_settings',
        'paquete_module_section'
    );
    
    add_settings_field(
        'paquete_guard_access',
        'Acceso de Guardia',
        'paquete_guard_access_render',
        'paquete_module_settings',
        'paquete_module_section'
    );
    
    add_settings_field(
        'paquete_admin_access',
        'Acceso de Administrador',
        'paquete_admin_access_render',
        'paquete_module_settings',
        'paquete_module_section'
    );
}

add_action('admin_init', 'paqueteria_settings_init');

// Callbacks para renderizar campos.
function paquete_module_active_render() {
    $options = get_option('paquete_module_active');
    ?>
    <input type="checkbox" name="paquete_module_active" value="1" <?php checked($options, 1); ?>>
    <?php
}

function paquete_email_notifications_render() {
    $options = get_option('paquete_email_notifications');
    ?>
    <input type="checkbox" name="paquete_email_notifications" value="1" <?php checked($options, 1); ?>>
    <?php
}

function paquete_sender_field_label_render() {
    $options = get_option('paquete_sender_field_label');
    ?>
    <input type="text" name="paquete_sender_field_label" value="<?php echo esc_attr($options); ?>">
    <?php
}

function paquete_description_field_label_render() {
    $options = get_option('paquete_description_field_label');
    ?>
    <input type="text" name="paquete_description_field_label" value="<?php echo esc_attr($options); ?>">
    <?php
}

function paquete_guard_access_render() {
    $options = get_option('paquete_guard_access');
    ?>
    <input type="checkbox" name="paquete_guard_access" value="1" <?php checked($options, 1); ?>>
    <?php
}

function paquete_admin_access_render() {
    $options = get_option('paquete_admin_access');
    ?>
    <input type="checkbox" name="paquete_admin_access" value="1" <?php checked($options, 1); ?>>
    <?php
}

function paquete_module_section_callback() {
    echo 'Configura los ajustes del módulo de paquetería aquí.';
}
