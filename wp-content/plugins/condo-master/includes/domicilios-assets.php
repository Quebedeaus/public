<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Encolar scripts y estilos para la administración de domicilios
function domicilios_enqueue_assets($hook) {
    // Verificar si estamos en la página de administración de domicilios
    if ($hook !== 'toplevel_page_domicilios-admin') {
        return;
    }

    // Registrar y encolar el archivo de JavaScript para la administración de domicilios
    wp_enqueue_script('domicilios-admin-js', plugins_url('js/domicilios-admin.js', __FILE__), array('jquery'), '1.0', true);

    // Localizar el script para pasar datos PHP a JavaScript
    wp_localize_script('domicilios-admin-js', 'domiciliosAdmin', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('domicilios_admin_nonce')
    ));

    // Encolar el archivo de estilo para la administración de domicilios
    wp_enqueue_style('domicilios-admin-css', plugins_url('css/domicilios-admin.css', __FILE__));
}

// Hook para encolar scripts y estilos en la página de administración
add_action('admin_enqueue_scripts', 'domicilios_enqueue_assets');
?>
