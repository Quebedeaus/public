<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Función para crear los menús de administración
function condo_master_admin_menu() {
    // Menú principal del plugin
    add_menu_page(
        'Condo Master',             // Título de la página
        'Condo Master',             // Título del menú
        'manage_options',           // Capacidad
        'condo-master',             // Slug del menú
        'condo_master_admin_page',  // Función del contenido
        'dashicons-admin-home',     // Ícono del menú
        6                           // Posición
    );

    // Submenús
    add_submenu_page(
        'condo-master',              // Slug del menú padre
        'Dashboard',                 // Título de la página
        'Dashboard',                 // Título del submenú
        'manage_options',            // Capacidad
        'condo-master',              // Slug del submenú
        'condo_master_admin_page'    // Función del contenido
    );

    add_submenu_page(
        'condo-master',
        'Configuración',
        'Configuración',
        'manage_options',
        'condo-master-settings',
        'condo_master_settings_page'
    );

    add_submenu_page(
        'condo-master',
        'Votaciones',
        'Votaciones',
        'manage_options',
        'condo-master-votaciones',
        'condo_master_votaciones_page'
    );

    add_submenu_page(
        'condo-master',
        'Lista Negra',
        'Lista Negra',
        'manage_options',
        'condo-master-lista-negra',
        'condo_master_lista_negra_page'
    );

    add_submenu_page(
        'condo-master',
        'Proveedores',
        'Proveedores',
        'manage_options',
        'condo-master-proveedores',
        'condo_master_proveedores_page'
    );

    add_submenu_page(
        'condo-master',
        'Encuestas',
        'Encuestas',
        'manage_options',
        'condo-master-encuestas',
        'condo_master_encuestas_page'
    );

    add_submenu_page(
        'condo-master',
        'Ayuda',
        'Ayuda',
        'manage_options',
        'condo-master-ayuda',
        'condo_master_ayuda_page'
    );
}
add_action('admin_menu', 'condo_master_admin_menu');

// Función para el contenido de la página principal (Dashboard)
function condo_master_admin_page() {
    ?>
    <div class="wrap">
        <h1>Condo Master Dashboard</h1>
        <p>Bienvenido al panel de administración de Condo Master.</p>
        <!-- Añadir aquí contenido del dashboard -->
    </div>
    <?php
}

// Función para el contenido de la página de configuración
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

// Función para registrar configuraciones
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

// Funciones para los submenús adicionales
function condo_master_votaciones_page() {
    ?>
    <div class="wrap">
        <h1>Gestión de Votaciones</h1>
        <p>Desde aquí podrás crear y gestionar las votaciones.</p>
    </div>
    <?php
}

function condo_master_lista_negra_page() {
    ?>
    <div class="wrap">
        <h1>Lista Negra</h1>
        <p>Gestión de la lista negra de visitantes y proveedores restringidos.</p>
    </div>
    <?php
}

function condo_master_proveedores_page() {
    ?>
    <div class="wrap">
        <h1>Gestión de Proveedores</h1>
        <p>Aquí podrás gestionar los proveedores recomendados y suscripciones de anuncios.</p>
    </div>
    <?php
}

function condo_master_encuestas_page() {
    ?>
    <div class="wrap">
        <h1>Encuestas de Calidad</h1>
        <p>Aquí podrás crear encuestas de satisfacción sobre los servicios del condominio.</p>
    </div>
    <?php
}

function condo_master_ayuda_page() {
    ?>
    <div class="wrap">
        <h1>Ayuda de Condo Master</h1>
        <p>Esta es la página de ayuda para el plugin Condo Master.</p>
    </div>
    <?php
}
