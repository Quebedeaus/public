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
require_once plugin_dir_path(__FILE__) . 'includes/domicilios_setup.php';


// Encolar scripts y estilos
function condo_master_enqueue_scripts() {
    if (is_admin()) {
        // Encolar scripts para domicilios
        wp_enqueue_script('domicilios-script', plugin_dir_url(__FILE__) . 'assets/js/domicilios-script.js', array('jquery'), null, true);
        // Encolar script para reservaciones
        wp_enqueue_script('reservaciones-script', plugin_dir_url(__FILE__) . 'assets/js/reservaciones-script.js', array('jquery'), null, true);
    } else {
        // Encolar scripts para el frontend
        wp_enqueue_script('reservaciones-script', plugin_dir_url(__FILE__) . 'assets/js/js/reservaciones-script.js', array('jquery'), null, true);
        wp_enqueue_style('reservaciones-style', plugin_dir_url(__FILE__) . 'assets/js/css/reservaciones.css');

        wp_enqueue_script('paqueteria-public', plugin_dir_url(__FILE__) . 'public/js/paqueteria-public.js', array('jquery'), null, true);
        wp_enqueue_style('paqueteria-style', plugin_dir_url(__FILE__) . 'public/css/paqueteria-public.css');

        // Localizar script para manejar AJAX en paquetería
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
}
add_action('admin_menu', 'condo_master_menu');

// Funciones de activación y desactivación del plugin.
function condo_master_activate() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Tabla de Paquetería
    $table_name = $wpdb->prefix . 'paqueteria';
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        residente_id bigint(20) UNSIGNED NOT NULL,
        paquete varchar(255) NOT NULL,
        fecha_entrega datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        status varchar(50) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // Agregar más tablas si es necesario para otros módulos
}

function condo_master_deactivate() {
    // No eliminamos las tablas para preservar los datos
}

// Agregar shortcodes para mostrar la información en el frontend.
add_shortcode('paqueteria_info', 'display_paqueteria_information');
add_shortcode('reservaciones_info', 'display_reservaciones_information');
add_shortcode('incident_reports', 'display_incident_reports');
add_shortcode('qr_generator', 'display_qr_generator');
add_shortcode('visitor_management', 'display_visitor_management');

// Definición de funciones para las páginas de administración
function condo_master_admin_page() {
    echo '<h1>Página de Administración de Condo Master</h1>';
}






// Funciones adicionales
function mostrar_pagina_paqueteria_admin() {
    echo '<h1>Paquetería Admin</h1>';
}






