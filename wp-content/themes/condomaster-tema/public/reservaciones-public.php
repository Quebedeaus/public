<?php

// ========================================
// =  Módulo: Gestión de Reservas de Amenidades
// =  Este módulo permite a los residentes reservar espacios comunes y a los administradores gestionarlos.
// ========================================

/**
 * Crear una nueva reserva de amenidad.
 *
 * @param int $user_id ID del usuario que realiza la reserva.
 * @param int $amenity_id ID de la amenidad a reservar.
 * @param string $reservation_date Fecha de la reserva (Y-m-d H:i:s).
 * @return bool True si la reserva se realizó correctamente, False en caso contrario.
 */
function condo_master_create_amenity_reservation($user_id, $amenity_id, $reservation_date) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amenity_reservations';

    $result = $wpdb->insert(
        $table_name,
        array(
            'user_id' => $user_id,
            'amenity_id' => $amenity_id,
            'reservation_date' => $reservation_date,
            'status' => 'pendiente',
            'created_at' => current_time('mysql'),
        )
    );

    return $result !== false;
}

/**
 * Listar las reservas de un residente.
 *
 * @param int $user_id ID del usuario.
 * @return array Lista de reservas realizadas por el usuario.
 */
function condo_master_list_reservations($user_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amenity_reservations';

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE user_id = %d ORDER BY reservation_date ASC",
        $user_id
    ));

    return $results;
}

/**
 * Administrar las reservas desde el dashboard.
 * Solo accesible para Administradores.
 *
 * @return void
 */
function condo_master_admin_manage_reservations() {
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'amenity_reservations';

    $reservations = $wpdb->get_results(
        "SELECT * FROM $table_name ORDER BY reservation_date DESC"
    );

    // Interfaz básica para administrar reservas (se puede mejorar con una tabla interactiva)
    echo '<h2>Gestionar Las Reservas de Amenidades</h2>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Usuario</th><th>Amenidad</th><th>Fecha de Reserva</th><th>Estado</th><th>Acciones</th></tr></thead>';
    echo '<tbody>';

    foreach ($reservations as $reservation) {
        echo '<tr>';
        echo '<td>' . esc_html(get_userdata($reservation->user_id)->display_name) . '</td>';
        echo '<td>' . esc_html($reservation->amenity_id) . '</td>'; // Podrías cambiar esto por el nombre de la amenidad.
        echo '<td>' . esc_html($reservation->reservation_date) . '</td>';
        echo '<td>' . esc_html($reservation->status) . '</td>';
        echo '<td>';
        // Botones de acción (se pueden expandir con más opciones como cancelar, modificar, etc.)
        echo '<a href="#">Aprobar</a> | <a href="#">Cancelar</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}
add_action('admin_menu', 'condo_master_admin_manage_reservations_menu');

/**
 * Agregar la opción de gestión de reservas al menú de administración.
 */
function condo_master_admin_manage_reservations_menu() {
    add_menu_page(
        'Gestionar Reservas',
        'Reservas',
        'manage_options',
        'condo-master-manage-reservations',
        'condo_master_admin_manage_reservations',
        'dashicons-calendar-alt',
        26
    );
}

// ========================================
// =  Fin del Módulo: Gestión de Reservas de Amenidades
// ========================================
