<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Encolar scripts y estilos para la administración de domicilios
function encolar_domicilios_admin_scripts($hook) {
    // Verificar si estamos en la página de administración de domicilios
    if ($hook !== 'toplevel_page_domicilios-admin') {
        return;
    }

    // Encolar estilo CSS
    wp_enqueue_style('domicilios-admin-style', plugin_dir_url(__FILE__) . 'css/domicilios-admin.css');

    // Encolar script JS
    wp_enqueue_script('domicilios-admin-script', plugin_dir_url(__FILE__) . 'js/domicilios-admin.js', array('jquery'), null, true);

    // Pasar datos al script JS
    wp_localize_script('domicilios-admin-script', 'domicilios_admin_data', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('domicilios_admin_nonce')
    ));
}

// Hook para encolar scripts y estilos
add_action('admin_enqueue_scripts', 'encolar_domicilios_admin_scripts');

function domicilios_setup_scripts() {
    ?>
    <script type="text/javascript">
        function toggleFields(tipo) {
            document.getElementById('residencial-fields').style.display = (tipo === 'residencial' || tipo === 'mixto') ? 'block' : 'none';
            document.getElementById('industrial-fields').style.display = (tipo === 'industrial' || tipo === 'mixto') ? 'block' : 'none';
            document.getElementById('comercial-fields').style.display = (tipo === 'comercial' || tipo === 'mixto') ? 'block' : 'none';
        }

        // Inicializar los campos basados en la selección actual
        document.addEventListener('DOMContentLoaded', function() {
            var tipo = document.querySelector('input[name="domicilios_config[tipo]"]:checked');
            if (tipo) {
                toggleFields(tipo.value);
            }
        });
    </script>
    <?php
}
add_action('admin_footer', 'domicilios_setup_scripts');

