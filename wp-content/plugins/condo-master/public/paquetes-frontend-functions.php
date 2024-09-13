<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Función para obtener paquetes desde el frontend
function obtener_paquetes_frontend($args = array()) {
    global $wpdb;
    $tabla_paquetes = $wpdb->prefix . 'paquetes';

    $defaults = array(
        'number' => -1,
        'offset' => 0,
        'orderby' => 'name',
        'order' => 'ASC'
    );
    $args = wp_parse_args($args, $defaults);

    $query = $wpdb->prepare(
        "SELECT * FROM $tabla_paquetes
        ORDER BY %s %s
        LIMIT %d OFFSET %d",
        $args['orderby'],
        $args['order'],
        $args['number'],
        $args['offset']
    );

    return $wpdb->get_results($query);
}

// Función para mostrar los paquetes en el frontend
function mostrar_paquetes_frontend() {
    $paquetes = obtener_paquetes_frontend(array('number' => 10));

    if (!empty($paquetes)) {
        echo '<ul class="paquetes-list">';
        foreach ($paquetes as $paquete) {
            echo '<li>' . esc_html($paquete->nombre) . ' - ' . esc_html($paquete->descripcion) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No hay paquetes disponibles.</p>';
    }
}

// Shortcode para mostrar los paquetes
function paquetes_shortcode() {
    ob_start();
    mostrar_paquetes_frontend();
    return ob_get_clean();
}
add_shortcode('mostrar_paquetes', 'paquetes_shortcode');
?>
