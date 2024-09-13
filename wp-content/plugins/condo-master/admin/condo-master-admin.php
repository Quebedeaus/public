<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

function condo_master_admin_page() {
    ?>
    <div class="wrap">
        <h1>Condo Master Dashboard</h1>
        <p>Bienvenido al panel de administración de Condo Master.</p>
        <!-- Añadir aquí contenido del dashboard -->
    </div>
    <?php
}

function condo_master_settings_page() {
    ?>
    <div class="wrap">
        <h1>Configuración de Condo Master</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('condo_master_settings');
            do_settings_sections('condo_master_settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Registrar configuraciones
function condo_master_settings_init() {
    register_setting('condo_master_settings', 'condo_master_option_name');

    add_settings_section(
        'condo_master_settings_section',
        'Configuración General',
        'condo_master_settings_section_callback',
        'condo_master_settings'
    );

    add_settings_field(
        'condo_master_text_field',
        'Ejemplo de Campo',
        'condo_master_text_field_render',
        'condo_master_settings',
        'condo_master_settings_section'
    );
}

function condo_master_settings_section_callback() {
    echo 'Configura las opciones generales de Condo Master aquí:';
}

function condo_master_text_field_render() {
    $options = get_option('condo_master_option_name');
    ?>
    <input type="text" name="condo_master_option_name[condo_master_text_field]" value="<?php echo $options['condo_master_text_field'] ?? ''; ?>">
    <?php
}

add_action('admin_init', 'condo_master_settings_init');

// Función para ayuda
function ayuda_page() {
    ?>
    <div class="wrap">
        <h1>Ayuda de Condo Master</h1>
        <p>Esta es la página de ayuda para el plugin Condo Master.</p>
        <!-- Añadir aquí contenido de ayuda -->
    </div>
    <?php
}
?>
