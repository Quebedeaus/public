<?php
// Evitar acceso directo.
if (!defined('ABSPATH')) {
    exit;
}

// Agregar shortcode para el formulario de reservas
add_shortcode('reservaciones_form', 'reservaciones_form_shortcode');

function reservaciones_form_shortcode() {
    ob_start();
    ?>
    <h1>Reservar</h1>
    <form id="reservaciones-form">
        <label for="name">Nombre:</label>
        <input type="text" id="name" name="name" required>
        <br>
        <label for="date">Fecha:</label>
        <input type="date" id="date" name="date" required>
        <br>
        <label for="time">Hora:</label>
        <input type="time" id="time" name="time" required>
        <br>
        <label for="details">Detalles:</label>
        <textarea id="details" name="details"></textarea>
        <br>
        <input type="submit" value="Reservar">
    </form>
    <div id="reservaciones-response"></div>
    <?php
    return ob_get_clean();
}

// Manejar la reserva mediante AJAX
add_action('wp_ajax_reservaciones_submit', 'reservaciones_submit');
add_action('wp_ajax_nopriv_reservaciones_submit', 'reservaciones_submit');

function reservaciones_submit() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'reservaciones';

    $name = sanitize_text_field($_POST['name']);
    $date = sanitize_text_field($_POST['date']);
    $time = sanitize_text_field($_POST['time']);
    $details = sanitize_textarea_field($_POST['details']);

    $wpdb->insert($table_name, array(
        'name' => $name,
        'date' => $date,
        'time' => $time,
        'details' => $details,
        'status' => 'pendiente',
        'created_at' => current_time('mysql')
    ));

    wp_send_json_success('Reserva realizada con éxito.');
}
