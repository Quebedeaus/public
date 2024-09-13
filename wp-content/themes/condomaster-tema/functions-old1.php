<?php

// ========================================
// =  Módulo: Definición de Roles          =
// ========================================

function condomaster_add_roles() {
    // Verificar si el rol ya existe para evitar duplicados
    if (null === get_role('administrador_general')) {
        add_role('administrador_general', 'Administrador General', array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'manage_options' => true,
        ));
    }

    if (null === get_role('supervisor_seguridad')) {
        add_role('supervisor_seguridad', 'Supervisor de Seguridad', array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }

    if (null === get_role('guardia_seguridad')) {
        add_role('guardia_seguridad', 'Guardia de Seguridad', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }

    if (null === get_role('residente_propietario')) {
        add_role('residente_propietario', 'Residente Propietario', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }

    if (null === get_role('residente_arrendatario')) {
        add_role('residente_arrendatario', 'Residente Arrendatario', array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_options' => false,
        ));
    }

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
// =  Fin del Módulo: Definición de Roles   =
// ========================================

// ========================================
// =  Módulo: Role Switcher                =
// ========================================

function condomaster_role_switcher() {
    // Verificar si el usuario tiene permisos para ver el Role Switcher
    if (current_user_can('manage_options')) {
        // Obtener los roles disponibles
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
 * Cambia el rol del usuario actual basado en la selección del Role Switcher
 */
function condomaster_handle_role_switch() {
    if (isset($_POST['change_role']) && isset($_POST['role_selector'])) {
        $new_role = sanitize_text_field($_POST['role_selector']);
        $user_id = get_current_user_id();
        $user = new WP_User($user_id);

        // Eliminar el rol actual
        foreach ($user->roles as $role) {
            $user->remove_role($role);
        }

        // Asignar el nuevo rol
        $user->add_role($new_role);
    }
}
add_action('init', 'condomaster_handle_role_switch');

// ========================================
// =  Fin del Módulo: Role Switcher        =
// ========================================

// ========================================
// =  Módulo: Registro de Menús            =
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
        'menu_predeterminado' => __('Menú Predeterminado'),
    ));
}
add_action('init', 'condomaster_register_menus');

// ========================================
// =  Fin del Módulo: Registro de Menús     =
// ========================================

// ========================================
// =  Módulo: Estilos y Scripts            =
// ========================================

function condomaster_enqueue_scripts() {
    // Estilos del tema
    wp_enqueue_style('theme-style', get_stylesheet_uri());

    // Scripts del tema
    // wp_enqueue_script('theme-script', get_template_directory_uri() . '/js/script.js', array('jquery'), null, true);
}
add_action('wp_enqueue_scripts', 'condomaster_enqueue_scripts');

// ========================================
// =  Fin del Módulo: Estilos y Scripts    =
// ========================================


// Enqueue scripts and styles
function enqueue_guardia_scripts() {
    wp_enqueue_script('guardia-script', get_template_directory_uri() . '/js/guardia.js', array('jquery'), null, true);
    wp_enqueue_style('guardia-style', get_template_directory_uri() . '/css/guardia.css');
}
add_action('wp_enqueue_scripts', 'enqueue_guardia_scripts');

// Save task completion or omission
function save_task_status() {
    if (isset($_POST['task_id'])) {
        $task_id = intval($_POST['task_id']);
        $status = sanitize_text_field($_POST['status']);
        $evidence = isset($_FILES['evidence']) ? $_FILES['evidence'] : null;
        $reason = isset($_POST['reason']) ? sanitize_text_field($_POST['reason']) : '';

        // Update task status and evidence
        // Your logic here

        wp_send_json_success('Task updated successfully.');
    }
}
add_action('wp_ajax_save_task_status', 'save_task_status');
function condomaster_menu_items_by_role() {
    $user = wp_get_current_user();
    
    // Menú para el condominio maestro
    if (in_array('condominio_maestro', $user->roles)) {
        add_menu_page(
            'Eventos (Condominio Maestro)', 
            'Eventos (Maestro)', 
            'manage_options', 
            'maestro_eventos', 
            'maestro_eventos_page', 
            'dashicons-calendar-alt', 
            6
        );
        add_submenu_page(
            'maestro_eventos', 
            'Crear Evento', 
            'Crear Evento', 
            'manage_options', 
            'crear-evento-maestro', 
            'crear_evento_maestro_page'
        );
        add_submenu_page(
            'maestro_eventos', 
            'Ver Eventos', 
            'Ver Eventos', 
            'manage_options', 
            'ver-eventos-maestro', 
            'ver_eventos_maestro_page'
        );
    }
    
    // Menú para los subcondominios
    add_menu_page(
        'Eventos (Subcondominio)', 
        'Eventos (Sub)', 
        'manage_options', 
        'sub_eventos', 
        'sub_eventos_page', 
        'dashicons-calendar-alt', 
        7
    );
    add_submenu_page(
        'sub_eventos', 
        'Crear Evento', 
        'Crear Evento', 
        'manage_options', 
        'crear-evento-sub', 
        'crear_evento_sub_page'
    );
    add_submenu_page(
        'sub_eventos', 
        'Ver Eventos', 
        'Ver Eventos', 
        'manage_options', 
        'ver-eventos-sub', 
        'ver_eventos_sub_page'
    );
}
add_action('admin_menu', 'condomaster_menu_items_by_role');
function maestro_eventos_page() {
    echo '<div class="wrap"><h1>Eventos del Condominio Maestro</h1><p>Aquí puedes gestionar los eventos del condominio maestro.</p></div>';
}

function crear_evento_maestro_page() {
    echo '<div class="wrap"><h1>Crear Evento en el Condominio Maestro</h1><p>Formulario para crear un nuevo evento.</p></div>';
}

function ver_eventos_maestro_page() {
    echo '<div class="wrap"><h1>Ver Eventos del Condominio Maestro</h1><p>Lista de eventos creados.</p></div>';
}

function sub_eventos_page() {
    echo '<div class="wrap"><h1>Eventos del Subcondominio</h1><p>Aquí puedes gestionar los eventos del subcondominio.</p></div>';
}

function crear_evento_sub_page() {
    echo '<div class="wrap"><h1>Crear Evento en Subcondominio</h1><p>Formulario para crear un nuevo evento.</p></div>';
}

function ver_eventos_sub_page() {
    echo '<div class="wrap"><h1>Ver Eventos del Subcondominio</h1><p>Lista de eventos creados.</p></div>';
}
function condo_master_menu() {
    // Add main menu
    add_menu_page(
        __('Condo Master', 'textdomain'), 
        'Condo Master', 
        'manage_options', 
        'condo-master', 
        'condo_master_dashboard', 
        'dashicons-admin-home', 
        2
    );

    // Add submenu for Paquetería
    add_submenu_page(
        'condo-master', 
        __('Paquetería', 'textdomain'), 
        'Paquetería', 
        'manage_options', 
        'condo-master-paqueteria', 
        'condo_master_paqueteria'
    );

    // Add submenu for Rondines de Seguridad
    add_submenu_page(
        'condo-master', 
        __('Rondines de Seguridad', 'textdomain'), 
        'Rondines de Seguridad', 
        'manage_options', 
        'condo-master-rondines', 
        'condo_master_rondines'
    );

    // Add submenu for Usuarios y Roles
    add_submenu_page(
        'condo-master', 
        __('Usuarios y Roles', 'textdomain'), 
        'Usuarios y Roles', 
        'manage_options', 
        'condo-master-usuarios', 
        'condo_master_usuarios'
    );

    // Add submenu for Reservaciones de Instalaciones
    add_submenu_page(
        'condo-master', 
        __('Reservaciones de Instalaciones', 'textdomain'), 
        'Reservaciones de Instalaciones', 
        'manage_options', 
        'condo-master-reservaciones', 
        'condo_master_reservaciones'
    );
}
add_action('admin_menu', 'condo_master_menu');
