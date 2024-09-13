<?php

// ========================================
// =  Módulo: Definición de Roles          =
// =  Este módulo define roles personalizados para el sistema de condominios.
// ========================================

function condomaster_add_roles() {
    // Agregar el rol "Administrador General" si no existe
    if (null === get_role('administrador_general')) {
        add_role('administrador_general', 'Administrador General', array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'manage_options' => true, // Permite acceder a opciones de administración
        ));
    }

    // Agregar el rol "Supervisor de Seguridad" si no existe
    if (null === get_role('supervisor_seguridad')) {
        add_role('supervisor_seguridad', 'Supervisor de Seguridad', array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }

    // Agregar el rol "Guardia de Seguridad" si no existe
    if (null === get_role('guardia_seguridad')) {
        add_role('guardia_seguridad', 'Guardia de Seguridad', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }

    // Agregar el rol "Residente Propietario" si no existe
    if (null === get_role('residente_propietario')) {
        add_role('residente_propietario', 'Residente Propietario', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }

    // Agregar el rol "Residente Arrendatario" si no existe
    if (null === get_role('residente_arrendatario')) {
        add_role('residente_arrendatario', 'Residente Arrendatario', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }

    // Agregar el rol "Residente Huésped" si no existe
    if (null === get_role('residente_huesped')) {
        add_role('residente_huesped', 'Residente Huésped', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }
}
add_action('init', 'condomaster_add_roles');

// ========================================
// =  Fin del Módulo: Definición de Roles  =
// ========================================

// ========================================
// =  Módulo: Role Switcher                =
// =  Este módulo permite a los usuarios cambiar entre roles si tienen los permisos necesarios.
// ========================================

function condomaster_role_switcher() {
    // Solo mostrar el Role Switcher a usuarios con capacidad de 'manage_options'
    if (current_user_can('manage_options')) {
        // Obtener roles disponibles en el sistema
        $roles = wp_roles()->roles;
        $current_role = wp_get_current_user()->roles[0];
        ?>
        <div id="role-switcher" style="position: fixed; bottom: 10px; right: 10px; background-color: #333; color: #fff; padding: 10px; border-radius: 5px; z-index: 9999;">
            <form method="post">
                <label for="role_selector">Cambiar Rol:</label>
                <select name="role_selector" id="role_selector">
                    <?php foreach ($roles as $role => $details) : ?>
                        <option value="<?php echo esc_attr($role); ?>" <?php selected($current_role, $role); ?>>
                            <?php echo translate_user_role($details['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" name="change_role" value="Cambiar" />
            </form>
        </div>
        <?php
    }
}
add_action('admin_footer', 'condomaster_role_switcher');

/**
 * Cambia el rol del usuario actual basado en la selección del Role Switcher.
 * Se ejecuta cuando el usuario envía el formulario para cambiar su rol.
 */
function condomaster_handle_role_switch() {
    if (isset($_POST['change_role']) && isset($_POST['role_selector'])) {
        $new_role = sanitize_text_field($_POST['role_selector']);
        $user_id = get_current_user_id();
        $user = new WP_User($user_id);

        // Eliminar todos los roles actuales del usuario
        foreach ($user->roles as $role) {
            $user->remove_role($role);
        }

        // Asignar el nuevo rol seleccionado
        $user->add_role($new_role);
    }
}
add_action('init', 'condomaster_handle_role_switch');

// ========================================
// =  Fin del Módulo: Role Switcher        =
// ========================================

// ========================================
// =  Módulo: Registro de Menús            =
// =  Este módulo registra menús personalizados para diferentes roles de usuario.
// ========================================

function condomaster_register_menus() {
    register_nav_menus(array(
        'menu_administrador' => __('Menú Administrador'),
        'menu_supervisor' => __('Menú Supervisor de Seguridad'),
        'menu_guardia' => __('Menú Guardia de Seguridad'),
        'menu_propietario' => __('Menú Residente Propietario'),
        'menu_arrendatario' => __('Menú Residente Arrendatario'),
        'menu_huesped' => __('Menú Residente Huésped'),
        'menu_visitante' => __('Menú Visitante'),
        'menu_predeterminado' => __('Menú Predeterminado'), // Menú de respaldo si no hay menú específico
    ));
}
add_action('init', 'condomaster_register_menus');

// ========================================
// =  Fin del Módulo: Registro de Menús    =
// ========================================

// ========================================
// =  Módulo: Estilos y Scripts            =
// =  Este módulo encola los estilos y scripts necesarios para el tema.
// ========================================

function condomaster_enqueue_scripts() {
    // Encolar el estilo principal del tema
    wp_enqueue_style('theme-style', get_stylesheet_uri());

    // Encolar scripts del tema si es necesario (comentar si no se utiliza)
    // wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'condomaster_enqueue_scripts');

// ========================================
// =  Fin del Módulo: Estilos y Scripts    =
// ========================================

// ========================================
// =  Módulo: Scripts y Estilos para Guardia de Seguridad
// =  Este módulo encola scripts y estilos específicos para el rol de Guardia de Seguridad.
// ========================================

function enqueue_guardia_scripts() {
    wp_enqueue_script('guardia-script', get_template_directory_uri() . '/js/guardia.js', array('jquery'), null, true);
    wp_enqueue_style('guardia-style', get_template_directory_uri() . '/css/guardia.css');
}
add_action('wp_enqueue_scripts', 'enqueue_guardia_scripts');

// ========================================
// =  Fin del Módulo: Scripts y Estilos para Guardia de Seguridad
// ========================================

// ========================================
// =  Módulo: Guardar estado de tareas     =
// =  Este módulo permite guardar el estado de las tareas, incluyendo evidencia y razones.
// ========================================

function save_task_status() {
    if (isset($_POST['task_id'])) {
        $task_id = intval($_POST['task_id']);
        $status = sanitize_text_field($_POST['status']);
        $evidence = isset($_FILES['evidence']) ? $_FILES['evidence'] : null;
        $reason = isset($_POST['reason']) ? sanitize_text_field($_POST['reason']) : '';

        // Aquí puedes agregar la lógica para guardar el estado de la tarea y la evidencia
        // Ejemplo: actualizar un meta campo o guardar en la base de datos

        wp_send_json_success('Task updated successfully.');
    }
}
add_action('wp_ajax_save_task_status', 'save_task_status');

// ========================================
// =  Fin del Módulo: Guardar estado de tareas
// ========================================

// ========================================
// =  Módulo: Menús de Eventos por Rol     =
// =  Este módulo define los elementos del menú basados en el rol del usuario.
// ========================================

function condomaster_menu_items_by_role() {
    $user = wp_get_current_user();
    $role = $user->roles[0]; // Asumiendo que solo tienen un rol

    // Mostrar menú de administrador solo si el usuario es administrador
    if ($role == 'administrador_general') {
        add_menu_page('Condominio Maestro', 'Condominio Maestro', 'manage_options', 'condo_master', 'condo_master_dashboard');
        add_submenu_page('condo_master', 'Paquetería', 'Paquetería', 'manage_options', 'condo_master_paqueteria', 'condo_master_paqueteria');
        add_submenu_page('condo_master', 'Seguridad', 'Seguridad', 'manage_options', 'condo_master_seguridad', 'condo_master_seguridad');
    }

    // Otros roles y sus respectivos menús
    // ...
}
add_action('admin_menu', 'condomaster_menu_items_by_role');

// ========================================
// =  Fin del Módulo: Menús de Eventos por Rol
// ========================================


// ========================================
// =  Módulo: Funciones Personalizadas     =
// =  Este módulo contiene funciones específicas para el funcionamiento del sistema de condominios.
// ========================================

/**
 * Muestra un mensaje de bienvenida en el dashboard para el Administrador General.
 */
function condo_master_welcome_message() {
    $user = wp_get_current_user();
    $role = $user->roles[0];

    // Mostrar el mensaje solo para el rol de 'Administrador General'
    if ($role == 'administrador_general') {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p>¡Bienvenido, ' . esc_html($user->display_name) . '! Al sistema de gestión del Condominio Maestro.</p>';
        echo '</div>';
    }
}
add_action('admin_notices', 'condo_master_welcome_message');

// ========================================
// =  Fin del Módulo: Funciones Personalizadas
// ========================================

// ========================================
// =  Módulo: Shortcodes                   =
// =  Este módulo define los shortcodes personalizados para usar en el tema.
// ========================================

/**
 * Shortcode para mostrar información del residente en una página o entrada.
 * Uso: [resident_info]
 */
function condo_master_resident_info_shortcode($atts) {
    $user = wp_get_current_user();
    $role = $user->roles[0];
    
    // Solo mostrar información para residentes
    if (in_array($role, array('residente_propietario', 'residente_arrendatario', 'residente_huesped'))) {
        ob_start();
        ?>
        <div class="resident-info">
            <p><strong>Nombre:</strong> <?php echo esc_html($user->display_name); ?></p>
            <p><strong>Email:</strong> <?php echo esc_html($user->user_email); ?></p>
            <p><strong>Rol:</strong> <?php echo esc_html(translate_user_role($role)); ?></p>
        </div>
        <?php
        return ob_get_clean();
    }
    return '';
}
add_shortcode('resident_info', 'condo_master_resident_info_shortcode');

// ========================================
// =  Fin del Módulo: Shortcodes           =
// ========================================

// ========================================
// =  Módulo: Widgets                      =
// =  Este módulo registra y configura widgets personalizados para el tema.
// ========================================

/**
 * Registra un widget personalizado que muestra la información del residente.
 */
function condo_master_register_widgets() {
    register_widget('Condo_Master_Resident_Widget');
}
add_action('widgets_init', 'condo_master_register_widgets');

/**
 * Clase para el widget personalizado que muestra la información del residente.
 */
class Condo_Master_Resident_Widget extends WP_Widget {
    
    function __construct() {
        parent::__construct(
            'condo_master_resident_widget',
            __('Resident Info Widget', 'text_domain'),
            array('description' => __('Un widget para mostrar la información del residente.', 'text_domain'))
        );
    }

    /**
     * Salida del widget en el frontend.
     */
    public function widget($args, $instance) {
        $user = wp_get_current_user();
        $role = $user->roles[0];

        if (in_array($role, array('residente_propietario', 'residente_arrendatario', 'residente_huesped'))) {
            echo $args['before_widget'];
            echo $args['before_title'] . esc_html__('Información del Residente', 'text_domain') . $args['after_title'];
            ?>
            <p><strong>Nombre:</strong> <?php echo esc_html($user->display_name); ?></p>
            <p><strong>Email:</strong> <?php echo esc_html($user->user_email); ?></p>
            <p><strong>Rol:</strong> <?php echo esc_html(translate_user_role($role)); ?></p>
            <?php
            echo $args['after_widget'];
        }
    }

    /**
     * Formulario de configuración del widget en el backend.
     */
    public function form($instance) {
        // Si se requiere personalización del widget en el backend, agregar campos aquí
    }

    /**
     * Actualiza la configuración del widget en el backend.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        // Actualizar la configuración del widget aquí
        return $instance;
    }
}

// ========================================
// =  Fin del Módulo: Widgets              =
// ========================================

// ========================================
// =  Módulo: Funcionalidad de Notificaciones
// =  Este módulo gestiona las notificaciones enviadas a los residentes y administradores.
// ========================================

/**
 * Enviar notificaciones a los residentes cuando haya actualizaciones importantes.
 *
 * @param string $message Mensaje de la notificación.
 */
function condo_master_notify_residents($message) {
    $residents = get_users(array(
        'role__in' => array('residente_propietario', 'residente_arrendatario', 'residente_huesped'),
    ));

    foreach ($residents as $resident) {
        // Enviar correo electrónico de notificación
        wp_mail($resident->user_email, 'Notificación del Condominio', $message);
    }
}

/**
 * Ejemplo de uso: Enviar notificación cuando se publica una nueva entrada.
 */
function condo_master_post_published_notification($ID, $post) {
    $message = 'Una nueva publicación ha sido publicada en el sitio. Visita el sitio para más detalles.';
    condo_master_notify_residents($message);
}
add_action('publish_post', 'condo_master_post_published_notification', 10, 2);

// ========================================
// =  Fin del Módulo: Funcionalidad de Notificaciones
// ========================================


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
    echo '<h2>Gestionar Reservas de Amenidades</h2>';
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

<?php
// Guardar campos personalizados en el perfil del usuario al registrar o actualizar
function save_custom_user_fields($user_id) {
    // Verificar si el usuario tiene permisos para editar el perfil
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }
    
    // Asegurarse de que los campos personalizados se envíen y guarden correctamente
    if (isset($_POST['domicilios_config'])) {
        $config = $_POST['domicilios_config'];
        
        // Actualizar los campos personalizados en el perfil del usuario
        update_user_meta($user_id, 'domicilios_config', $config);
    }
}
add_action('user_register', 'save_custom_user_fields');
add_action('profile_update', 'save_custom_user_fields');

// Mostrar campos personalizados en el perfil del usuario
function show_custom_user_fields($user) {
    $config = get_user_meta($user->ID, 'domicilios_config', true);
    ?>
    <h3>Información de Domicilios</h3>
    <table class="form-table">
        <tr>
            <th><label for="domicilios_config_tipo">Tipo de Fraccionamiento</label></th>
            <td>
                <input type="text" name="domicilios_config[tipo]" id="domicilios_config_tipo" value="<?php echo esc_attr(isset($config['tipo']) ? $config['tipo'] : ''); ?>" class="regular-text" />
                <p class="description">Introduce el tipo de fraccionamiento.</p>
            </td>
        </tr>
        
        <!-- Ejemplo de campos adicionales -->
        <tr>
            <th><label for="domicilios_config_edificio">Edificio</label></th>
            <td>
                <input type="checkbox" name="domicilios_config[edificio]" id="domicilios_config_edificio" value="1" <?php checked(1, isset($config['edificio']) ? $config['edificio'] : 0); ?> />
                <p class="description">Marque si el domicilio incluye un edificio.</p>
            </td>
        </tr>

        <!-- Agregar más campos personalizados aquí si es necesario -->
    </table>
    <?php
}
add_action('show_user_profile', 'show_custom_user_fields');
add_action('edit_user_profile', 'show_custom_user_fields');






// ========================================
// =  Fin del Módulo: Gestión de Reservas de Amenidades
// ========================================
