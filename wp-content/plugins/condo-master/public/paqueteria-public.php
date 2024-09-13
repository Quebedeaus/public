<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Include the functions file to avoid redeclaration
require_once plugin_dir_path(__FILE__) . '../includes/paqueteria-functions.php';

// Mostrar la información de paquetería en el frontend
function display_paqueteria_information() {
    // Verificar si el usuario está logueado
    if (!is_user_logged_in()) {
        echo '<p>Por favor, inicia sesión para ver tus paquetes.</p>';
        return;
    }

    // Obtener el ID del usuario actual
    $user_id = get_current_user_id();

    // Configurar filtros y ordenamiento
    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'fecha_entrega';
    $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';
    $status_filter = isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '';

    // Obtener los paquetes del usuario
    $paquetes = obtener_entregas_residente($user_id, $orderby, $order, $status_filter);

    if (is_wp_error($paquetes)) {
        echo '<p>Error al obtener los paquetes: ' . $paquetes->get_error_message() . '</p>';
        return;
    }

    // Mostrar la información
    echo '<div class="paqueteria-info">';
    echo '<h2>Mis Paquetes</h2>';

    // Formulario de filtros
    echo '<form method="get" id="paqueteria-filter-form">';
    echo '<input type="hidden" name="page_id" value="' . get_the_ID() . '">';
    echo '<select name="status">';
    echo '<option value="">Todos los estados</option>';
    echo '<option value="pendiente"' . selected($status_filter, 'pendiente', false) . '>Pendiente</option>';
    echo '<option value="entregado"' . selected($status_filter, 'entregado', false) . '>Entregado</option>';
    echo '</select>';
    echo '<input type="submit" value="Filtrar">';
    echo '</form>';

    if (!empty($paquetes)) {
        echo '<table class="paqueteria-table">';
        echo '<thead><tr>';
        echo '<th><a href="' . add_query_arg(array('orderby' => 'id', 'order' => ($orderby === 'id' && $order === 'ASC') ? 'DESC' : 'ASC')) . '">ID</a></th>';
        echo '<th><a href="' . add_query_arg(array('orderby' => 'paquete', 'order' => ($orderby === 'paquete' && $order === 'ASC') ? 'DESC' : 'ASC')) . '">Paquete</a></th>';
        echo '<th><a href="' . add_query_arg(array('orderby' => 'fecha_entrega', 'order' => ($orderby === 'fecha_entrega' && $order === 'ASC') ? 'DESC' : 'ASC')) . '">Fecha de Entrega</a></th>';
        echo '<th><a href="' . add_query_arg(array('orderby' => 'status', 'order' => ($orderby === 'status' && $order === 'ASC') ? 'DESC' : 'ASC')) . '">Estado</a></th>';
        echo '</tr></thead>';
        echo '<tbody>';
        foreach ($paquetes as $paquete) {
            echo '<tr>';
            echo '<td>' . esc_html($paquete->id) . '</td>';
            echo '<td>' . esc_html($paquete->paquete) . '</td>';
            echo '<td>' . esc_html($paquete->fecha_entrega) . '</td>';
            echo '<td>' . esc_html($paquete->status) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>No tienes paquetes registrados.</p>';
    }

    echo '</div>';
}

// Agregar el shortcode para mostrar la información en el frontend
function paqueteria_shortcode() {
    ob_start();
    display_paqueteria_information();
    return ob_get_clean();
}
add_shortcode('paqueteria_info', 'paqueteria_shortcode');

// Agregar estilos personalizados para la visualización en el frontend
function paqueteria_public_styles() {
    wp_enqueue_style('paqueteria-public', plugins_url('css/paqueteria-public.css', __FILE__));
}
add_action('wp_enqueue_scripts', 'paqueteria_public_styles');

// Función para manejar las actualizaciones AJAX
function paqueteria_ajax_update() {
    check_ajax_referer('paqueteria_update_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error('User not logged in');
    }

    ob_start();
    display_paqueteria_information();
    $html = ob_get_clean();

    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_paqueteria_update', 'paqueteria_ajax_update');
add_action('wp_ajax_nopriv_paqueteria_update', 'paqueteria_ajax_update');
?>
