<?php
/*
Plugin Name: Condo Master
Description: Plugin integral para gestionar reservaciones, paquetería y otros módulos relacionados con la administración de condominios.
Version: 1.3
Author: Tu Nombre
*/

// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

// Incluir Funciones Comunes y Archivos Necesarios.
require_once plugin_dir_path(__FILE__) . 'includes/reservaciones-common.php';
require_once plugin_dir_path(__FILE__) . 'includes/paqueteria-functions.php';
require_once plugin_dir_path(__FILE__) . 'admin/csv-upload.php';
require_once plugin_dir_path(__FILE__) . 'includes/domicilios_setup.php';
require_once plugin_dir_path(__FILE__) . 'includes/user-management.php';

// Incluir Módulos
require_once plugin_dir_path(__FILE__) . 'modules/paqueteria.php';
require_once plugin_dir_path(__FILE__) . 'modules/incident-reports.php';
require_once plugin_dir_path(__FILE__) . 'modules/qr-codegenerator.php';
require_once plugin_dir_path(__FILE__) . 'modules/visitor-management.php';
require_once plugin_dir_path(__FILE__) . 'admin/reservaciones-admin.php';
require_once plugin_dir_path(__FILE__) . 'public/paqueteria-public.php';
require_once plugin_dir_path(__FILE__) . 'admin/paqueteria-settings.php';

// Encolar scripts y estilos
function condo_master_enqueue_scripts() {
    if (is_admin()) {
        wp_enqueue_script('domicilios-script', plugin_dir_url(__FILE__) . 'assets/js/domicilios-script.js', array('jquery'), null, true);
        wp_enqueue_script('reservaciones-script', plugin_dir_url(__FILE__) . 'assets/js/reservaciones-script.js', array('jquery'), null, true);
    } else {
        wp_enqueue_script('reservaciones-script', plugin_dir_url(__FILE__) . 'assets/js/js/reservaciones-script.js', array('jquery'), null, true);
        wp_enqueue_style('reservaciones-style', plugin_dir_url(__FILE__) . 'assets/js/css/reservaciones.css');
        wp_enqueue_script('paqueteria-public', plugin_dir_url(__FILE__) . 'public/js/paqueteria-public.js', array('jquery'), null, true);
        wp_enqueue_style('paqueteria-style', plugin_dir_url(__FILE__) . 'public/css/paqueteria-public.css');
        wp_localize_script('paqueteria-public', 'paqueteria_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('paqueteria_update_nonce')
        ));
    }
}
add_action('wp_enqueue_scripts', 'condo_master_enqueue_scripts');
add_action('admin_enqueue_scripts', 'condo_master_enqueue_scripts');

// Registrar menús de administración.
function condo_master_menu() {
    add_menu_page(
        'Condo Master',
        'Condo Master',
        'manage_options',
        'condo-master-admin',
        'condo_master_admin_page',
        'dashicons-admin-multisite',
        2
    );

    add_submenu_page(
        'condo-master-admin',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'condo-master-admin',
        'condo_master_admin_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Reservaciones',
        'Reservaciones',
        'manage_options',
        'reservaciones-admin',
        'reservaciones_admin_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Paquetería',
        'Paquetería',
        'manage_options',
        'paqueteria-admin',
        'mostrar_pagina_paqueteria_admin'
    );

    add_submenu_page(
        'condo-master-admin',
        'Ajustes Paquetería',
        'Ajustes Paquetería',
        'manage_options',
        'paqueteria-settings',
        'paqueteria_settings_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Reportes de Incidentes',
        'Reportes de Incidentes',
        'manage_options',
        'incident-reports-admin',
        'incident_reports_admin_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Generador de QR',
        'Generador de QR',
        'manage_options',
        'qr-generator-admin',
        'qr_generator_admin_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Gestión de Visitantes',
        'Gestión de Visitantes',
        'manage_options',
        'visitor-management-admin',
        'visitor_management_admin_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Registro de Usuarios',
        'Registro de Usuarios',
        'manage_options',
        'custom_user_registration',
        'custom_user_registration_form'
    );

    add_submenu_page(
        'condo-master-admin',
        'Subir CSV',
        'Subir CSV',
        'manage_options',
        'upload_csv_file',
        'custom_csv_upload_form'
    );

    add_submenu_page(
        'condo-master-admin',
        'Configuración de Domicilios',
        'Configurar Domicilios',
        'manage_options',
        'domicilios',
        'domicilios_setup_page'
    );

    // Nuevos menús
    add_submenu_page(
        'condo-master-admin',
        'Configuración',
        'Configuración',
        'manage_options',
        'condo-master-settings',
        'condo_master_settings_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Votaciones',
        'Votaciones',
        'manage_options',
        'condo-master-votaciones',
        'condo_master_votaciones_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Lista Negra',
        'Lista Negra',
        'manage_options',
        'condo-master-lista-negra',
        'condo_master_lista_negra_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Proveedores',
        'Proveedores',
        'manage_options',
        'condo-master-proveedores',
        'condo_master_proveedores_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Encuestas',
        'Encuestas',
        'manage_options',
        'condo-master-encuestas',
        'condo_master_encuestas_page'
    );

    add_submenu_page(
        'condo-master-admin',
        'Ayuda',
        'Ayuda',
        'manage_options',
        'condo-master-ayuda',
        'condo_master_ayuda_page'
    );
}
add_action('admin_menu', 'condo_master_menu');

// Función para el contenido de la página principal (Dashboard)
function condo_master_admin_page() {
    ?>
    <div class="wrap">
        <h1>Página de Administración de Condo Master</h1>
    </div>
    <?php
}

// Funciones adicionales para los nuevos menús
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

function condo_master_votaciones_page() {
    ?>
    <div class="wrap">
        <h1>Gestión de Votaciones</h1>
    </div>
    <?php
}

function condo_master_lista_negra_page() {
    ?>
    <div class="wrap">
        <h1>Lista Negra</h1>
    </div>
    <?php
}

function condo_master_proveedores_page() {
    ?>
    <div class="wrap">
        <h1>Gestión de Proveedores</h1>
    </div>
    <?php
}

function condo_master_encuestas_page() {
    ?>
    <div class="wrap">
        <h1>Encuestas de Calidad</h1>
    </div>
    <?php
}

function condo_master_ayuda_page() {
    ?>
    <div class="wrap">
        <h1>Ayuda de Condo Master</h1>
    </div>
    <?php
}

function mostrar_pagina_paqueteria_admin() {
    echo '<h1>Paquetería Admin</h1>';
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
    <input type="text" name="condo_master_option_name[condo_master_text_field]" value="<?php echo $options['condo_master_text_field']; ?>">
    <?php
}

add_action('admin_init', 'condo_master_settings_init');
