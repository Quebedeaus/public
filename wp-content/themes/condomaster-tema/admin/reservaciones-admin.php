// ========================================
// =  Módulo: Gestión de Amenidades Reservables
// =  Este módulo permite a los administradores configurar las amenidades que pueden ser reservadas en cada subcondominio.
// ========================================

/**
 * Crear una nueva amenidad.
 *
 * @param string $amenity_name Nombre de la amenidad.
 * @param string $description Descripción de la amenidad.
 * @param int $subcondo_id ID del subcondominio al que pertenece la amenidad.
 * @return bool True si la amenidad se creó correctamente, False en caso contrario.
 */
function condo_master_create_amenity($amenity_name, $description, $subcondo_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amenities';

    $result = $wpdb->insert(
        $table_name,
        array(
            'amenity_name' => $amenity_name,
            'description' => $description,
            'subcondo_id' => $subcondo_id,
            'created_at' => current_time('mysql'),
        )
    );

    return $result !== false;
}

/**
 * Editar una amenidad existente.
 *
 * @param int $amenity_id ID de la amenidad a editar.
 * @param string $amenity_name Nuevo nombre de la amenidad.
 * @param string $description Nueva descripción de la amenidad.
 * @return bool True si la amenidad se actualizó correctamente, False en caso contrario.
 */
function condo_master_edit_amenity($amenity_id, $amenity_name, $description) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amenities';

    $result = $wpdb->update(
        $table_name,
        array(
            'amenity_name' => $amenity_name,
            'description' => $description,
        ),
        array('id' => $amenity_id)
    );

    return $result !== false;
}

/**
 * Eliminar una amenidad.
 *
 * @param int $amenity_id ID de la amenidad a eliminar.
 * @return bool True si la amenidad se eliminó correctamente, False en caso contrario.
 */
function condo_master_delete_amenity($amenity_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amenities';

    $result = $wpdb->delete(
        $table_name,
        array('id' => $amenity_id)
    );

    return $result !== false;
}

/**
 * Listar las amenidades disponibles en un subcondominio.
 *
 * @param int $subcondo_id ID del subcondominio.
 * @return array Lista de amenidades disponibles en el subcondominio.
 */
function condo_master_list_amenities($subcondo_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'amenities';

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE subcondo_id = %d ORDER BY amenity_name ASC",
        $subcondo_id
    ));

    return $results;
}

// ========================================
// =  Fin del Módulo: Gestión de Amenidades Reservables
// ========================================

/**
 * Menú de administración para gestionar amenidades.
 */
function condo_master_admin_manage_amenities_menu() {
    add_submenu_page(
        'condo-master-manage-reservations', // Menú principal
        'Gestionar Amenidades',             // Título de la página
        'Amenidades',                       // Título del menú
        'manage_options',                   // Capacidad requerida
        'condo-master-manage-amenities',    // Slug de la página
        'condo_master_admin_manage_amenities' // Función de contenido
    );
}
add_action('admin_menu', 'condo_master_admin_manage_amenities_menu');

/**
 * Página de administración para gestionar amenidades.
 */
function condo_master_admin_manage_amenities() {
    // Aquí construiríamos la interfaz para ver, agregar, editar y eliminar amenidades.
    // Esta interfaz puede incluir formularios para agregar/editar y una lista interactiva para administrar las amenidades.

    echo '<h2>Gestionar Amenidades</h2>';
    
    // Implementar el formulario para agregar/editar amenidades y una tabla para listar amenidades.
}
