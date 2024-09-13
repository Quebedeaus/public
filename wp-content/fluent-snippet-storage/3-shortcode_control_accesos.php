<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: shortcode_control_accesos
* @type: PHP
* @status: published
* @created_by: 
* @created_at: 
* @updated_at: 2024-08-30 01:00:39
* @is_valid: 
* @updated_by: 
* @priority: 10
* @run_at: all
* @load_as_file: 
* @condition: {"status":"no","run_if":"assertive","items":[[]]}
*/
?>
<?php if (!defined("ABSPATH")) { return;} // <Internal Doc End> ?>
<?php
// Evita el acceso directo
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Salir si se accede directamente
}

// Registro de roles personalizados
function crear_roles_personalizados() {
    add_role('guardia', 'Guardia', array(
        'read' => true,
        'edit_posts' => false,
        'delete_posts' => false,
    ));

    add_role('gerente_seguridad', 'Gerente de Seguridad', array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => false,
        'manage_options' => false,
    ));

    add_role('admin_condominio', 'Administrador de Condominio', array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => false,
        'manage_options' => true,
    ));

    add_role('admin_macro', 'Administrador Macro Caseta', array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'manage_options' => true,
    ));

    add_role('admin_general', 'Administrador General', array(
        'read' => true,
        'edit_posts' => true,
        'delete_posts' => true,
        'manage_options' => true,
    ));
}
add_action('init', 'crear_roles_personalizados');

// Asignación de permisos avanzados para el Administrador General
function asignar_permisos_avanzados() {
    $role = get_role('admin_general');
    $role->add_cap('delete_others_posts');
    $role->add_cap('delete_private_posts');
    $role->add_cap('delete_published_posts');
    $role->add_cap('edit_others_posts');
    $role->add_cap('edit_private_posts');
    $role->add_cap('edit_published_posts');
    $role->add_cap('manage_options');
}
add_action('admin_init', 'asignar_permisos_avanzados');

// Eliminar roles personalizados al desactivar el plugin
function eliminar_roles_personalizados() {
    remove_role('guardia');
    remove_role('gerente_seguridad');
    remove_role('admin_condominio');
    remove_role('admin_macro');
    remove_role('admin_general');
}
register_deactivation_hook(__FILE__, 'eliminar_roles_personalizados');


// Crear formulario de registro de paquetes
function formulario_registro_paquetes() {
    ob_start();
    ?>
    <form id="formulario_paquetes" method="post">
        <h2>Registro de Paquetes</h2>
        <label for="destinatario">Persona a quien viene dirigido el paquete:</label>
        <input type="text" id="destinatario" name="destinatario" required>
        
        <label for="subcondominio">Nombre del subcondominio:</label>
        <select id="subcondominio" name="subcondominio">
            <option value="Cerrada 1">Cerrada 1</option>
            <option value="Cerrada 2">Cerrada 2</option>
            <option value="Cerrada 3">Cerrada 3</option>
            <option value="Cerrada 4">Cerrada 4</option>
            <option value="Edificios">Edificios</option>
            <option value="Cumboto">Cumboto</option>
            <option value="Acumare">Acumare</option>
        </select>

        <label for="num_vivienda">Número de vivienda:</label>
        <input type="text" id="num_vivienda" name="num_vivienda" required>

        <label for="telefono">Número de teléfono:</label>
        <input type="tel" id="telefono" name="telefono" required>

        <label for="palabra_clave">Palabra clave (en mayúsculas):</label>
        <input type="text" id="palabra_clave" name="palabra_clave" style="text-transform:uppercase;">

        <label for="guardia">Nombre del guardia que recibe el paquete:</label>
        <input type="text" id="guardia" name="guardia" required>

        <input type="submit" name="registrar_paquete" value="Registrar Paquete">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('registro_paquetes', 'formulario_registro_paquetes');

// Procesar el formulario de registro de paquetes
function procesar_formulario_paquetes() {
    if (isset($_POST['registrar_paquete'])) {
        global $wpdb;
        $tabla_paquetes = $wpdb->prefix . 'paquetes';

        $destinatario = ucwords(strtolower(sanitize_text_field($_POST['destinatario'])));
        $subcondominio = sanitize_text_field($_POST['subcondominio']);
        $num_vivienda = sanitize_text_field($_POST['num_vivienda']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $palabra_clave = strtoupper(sanitize_text_field($_POST['palabra_clave']));
        $guardia = ucwords(strtolower(sanitize_text_field($_POST['guardia'])));
        $fecha_hora_registro = current_time('mysql');

        $wpdb->insert($tabla_paquetes, array(
            'destinatario' => $destinatario,
            'subcondominio' => $subcondominio,
            'num_vivienda' => $num_vivienda,
            'telefono' => $telefono,
            'palabra_clave' => $palabra_clave,
            'guardia_recibe' => $guardia,
            'fecha_hora_registro' => $fecha_hora_registro,
        ));
    }
}
add_action('init', 'procesar_formulario_paquetes');





// Crear tabla para eventos y visitantes asociados
function crear_tabla_eventos_y_visitantes() {
    global $wpdb;
    $tabla_eventos = $wpdb->prefix . 'eventos';
    $tabla_visitantes = $wpdb->prefix . 'visitantes_evento';

    $charset_collate = $wpdb->get_charset_collate();

    $sql1 = "CREATE TABLE $tabla_eventos (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre_evento varchar(255) NOT NULL,
        subcondominio_evento varchar(255) NOT NULL,
        reglamento_evento text NOT NULL,
        fecha_hora_evento datetime NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    $sql2 = "CREATE TABLE $tabla_visitantes (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nombre_visitante varchar(255) NOT NULL,
        evento_id mediumint(9) NOT NULL,
        tipo_visitante varchar(255) NOT NULL,
        fecha_hora_registro datetime NOT NULL,
        PRIMARY KEY (id),
        FOREIGN KEY (evento_id) REFERENCES $tabla_eventos(id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql1);
    dbDelta($sql2);
}
register_activation_hook(__FILE__, 'crear_tabla_eventos_y_visitantes');

// Registro de visitantes asociados a un evento
function formulario_registro_visitantes_evento() {
    ob_start();
    ?>
    <form id="formulario_visitantes_evento" method="post">
        <h2>Registro de Visitantes para Evento</h2>

        <label for="nombre_evento_asociado">Seleccione el evento:</label>
        <select id="nombre_evento_asociado" name="nombre_evento_asociado">
            <?php
            global $wpdb;
            $tabla_eventos = $wpdb->prefix . 'eventos';
            $eventos = $wpdb->get_results("SELECT id, nombre_evento FROM $tabla_eventos");

            foreach ($eventos as $evento) {
                echo "<option value='" . esc_attr($evento->id) . "'>" . esc_html($evento->nombre_evento) . "</option>";
            }
            ?>
        </select>

        <label for="nombre_visitante">Nombre del visitante:</label>
        <input type="text" id="nombre_visitante" name="nombre_visitante" required>

        <label for="tipo_visitante">Tipo de visitante:</label>
        <select id="tipo_visitante" name="tipo_visitante">
            <option value="visitante">Visitante</option>
            <option value="asistente_evento">Asistente a Evento</option>
            <option value="huésped">Huésped</option>
        </select>

        <input type="submit" name="registrar_visitante_evento" value="Registrar Visitante">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('registro_visitantes_evento', 'formulario_registro_visitantes_evento');

// Procesar el registro de visitantes asociados a eventos
function procesar_formulario_visitantes_evento() {
    if (isset($_POST['registrar_visitante_evento'])) {
        global $wpdb;
        $tabla_visitantes = $wpdb->prefix . 'visitantes_evento';

        $nombre_visitante = ucwords(strtolower(sanitize_text_field($_POST['nombre_visitante'])));
        $evento_id = sanitize_text_field($_POST['nombre_evento_asociado']);
        $tipo_visitante = sanitize_text_field($_POST['tipo_visitante']);
        $fecha_hora_registro = current_time('mysql');

        $wpdb->insert($tabla_visitantes, array(
            'nombre_visitante' => $nombre_visitante,
            'evento_id' => $evento_id,
            'tipo_visitante' => $tipo_visitante,
            'fecha_hora_registro' => $fecha_hora_registro,
        ));
    }
}
add_action('init', 'procesar_formulario_visitantes_evento');




// Crear tabla para reglamentos
function crear_tabla_reglamentos() {
    global $wpdb;
    $tabla_reglamentos = $wpdb->prefix . 'reglamentos';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $tabla_reglamentos (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        subcondominio varchar(255) NOT NULL,
        tipo_reglamento varchar(255) NOT NULL,
        contenido_reglamento text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'crear_tabla_reglamentos');

// Formulario para cargar y editar reglamentos
function formulario_reglamento() {
    ob_start();
    ?>
    <form id="formulario_reglamento" method="post">
        <h2>Gestión de Reglamentos</h2>
        
        <label for="subcondominio_reglamento">Seleccione el subcondominio:</label>
        <select id="subcondominio_reglamento" name="subcondominio_reglamento">
            <option value="Macro Condominio">Macro Condominio</option>
            <option value="Cerrada 1">Cerrada 1</option>
            <option value="Cerrada 2">Cerrada 2</option>
            <option value="Cerrada 3">Cerrada 3</option>
            <option value="Cerrada 4">Cerrada 4</option>
            <option value="Edificios">Edificios</option>
            <option value="Cumboto">Cumboto</option>
            <option value="Acumare">Acumare</option>
        </select>

        <label for="tipo_reglamento">Tipo de reglamento:</label>
        <select id="tipo_reglamento" name="tipo_reglamento">
            <option value="visitantes">Reglamento de Visitantes y Asistentes</option>
            <option value="general">Reglamento General del Subcondominio</option>
        </select>

        <label for="contenido_reglamento">Contenido del reglamento:</label>
        <textarea id="contenido_reglamento" name="contenido_reglamento" rows="10"></textarea>

        <input type="submit" name="guardar_reglamento" value="Guardar Reglamento">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('gestion_reglamento', 'formulario_reglamento');

// Procesar el formulario de reglamentos
function procesar_formulario_reglamento() {
    if (isset($_POST['guardar_reglamento'])) {
        global $wpdb;
        $tabla_reglamentos = $wpdb->prefix . 'reglamentos';

        $subcondominio = sanitize_text_field($_POST['subcondominio_reglamento']);
        $tipo_reglamento = sanitize_text_field($_POST['tipo_reglamento']);
        $contenido_reglamento = sanitize_textarea_field($_POST['contenido_reglamento']);

        $existe_reglamento = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $tabla_reglamentos WHERE subcondominio = %s AND tipo_reglamento = %s",
            $subcondominio, $tipo_reglamento
        ));

        if ($existe_reglamento) {
            $wpdb->update(
                $tabla_reglamentos,
                array('contenido_reglamento' => $contenido_reglamento),
                array('id' => $existe_reglamento)
            );
        } else {
            $wpdb->insert($tabla_reglamentos, array(
                'subcondominio' => $subcondominio,
                'tipo_reglamento' => $tipo_reglamento,
                'contenido_reglamento' => $contenido_reglamento,
            ));
        }
    }
}
add_action('init', 'procesar_formulario_reglamento');

// Consultar reglamento en el frontend
function mostrar_reglamento($atts) {
    global $wpdb;
    $tabla_reglamentos = $wpdb->prefix . 'reglamentos';

    $atts = shortcode_atts(array(
        'subcondominio' => 'Macro Condominio',
        'tipo' => 'general',
    ), $atts);

    $resultado = $wpdb->get_var($wpdb->prepare(
        "SELECT contenido_reglamento FROM $tabla_reglamentos WHERE subcondominio = %s AND tipo_reglamento = %s",
        $atts['subcondominio'], $atts['tipo']
    ));

    if ($resultado) {
        return wpautop($resultado);
    } else {
        return '<p>No se encontró el reglamento solicitado.</p>';
    }
}
add_shortcode('consultar_reglamento', 'mostrar_reglamento');





// Programar notificación para eventos próximos
function programar_notificacion_eventos() {
    if (!wp_next_scheduled('enviar_notificaciones_eventos_proximos')) {
        wp_schedule_event(time(), 'hourly', 'enviar_notificaciones_eventos_proximos');
    }
}
add_action('wp', 'programar_notificacion_eventos');

// Enviar notificaciones de eventos próximos
function enviar_notificaciones_eventos_proximos() {
    global $wpdb;
    $tabla_eventos = $wpdb->prefix . 'eventos';

    $eventos_proximos = $wpdb->get_results("SELECT * FROM $tabla_eventos WHERE fecha_hora_evento > NOW() AND fecha_hora_evento < DATE_ADD(NOW(), INTERVAL 1 DAY)");

    foreach ($eventos_proximos as $evento) {
        $subcondominio = $evento->subcondominio_evento;
        $nombre_evento = $evento->nombre_evento;
        $fecha_evento = date('d-m-Y H:i', strtotime($evento->fecha_hora_evento));
        
        // Obtener administradores del subcondominio
        $admins = get_users(array('role' => 'administrator'));
        foreach ($admins as $admin) {
            $email_admin = $admin->user_email;

            // Enviar correo
            wp_mail(
                $email_admin,
                "Recordatorio: Evento Próximo en $subcondominio",
                "Estimado Administrador,\n\nLe recordamos que el evento '$nombre_evento' en el subcondominio '$subcondominio' se llevará a cabo el $fecha_evento.\n\nSaludos,\nAdministración"
            );
        }
    }
}
add_action('enviar_notificaciones_eventos_proximos', 'enviar_notificaciones_eventos_proximos');

// Desactivar notificaciones programadas al desactivar el plugin
function desactivar_notificaciones_programadas() {
    wp_clear_scheduled_hook('enviar_notificaciones_eventos_proximos');
}
register_deactivation_hook(__FILE__, 'desactivar_notificaciones_programadas');

// Funciones automáticas para gestionar accesos y autorizaciones
function gestionar_accesos_y_autorizaciones() {
    global $wpdb;
    $tabla_visitantes = $wpdb->prefix . 'visitantes_evento';
    $tabla_eventos = $wpdb->prefix . 'eventos';

    // Obtener visitantes registrados para eventos futuros
    $visitantes = $wpdb->get_results("SELECT * FROM $tabla_visitantes WHERE evento_id IN (SELECT id FROM $tabla_eventos WHERE fecha_hora_evento > NOW())");

    foreach ($visitantes as $visitante) {
        // Aquí puedes agregar la lógica para gestionar accesos o autorizaciones según el tipo de visitante
        // Por ejemplo, enviar notificaciones, marcar accesos en base de datos, etc.
    }
}
add_action('wp', 'gestionar_accesos_y_autorizaciones');

// Configuración de notificaciones y automatizaciones
function formulario_configuracion_notificaciones() {
    ob_start();
    ?>
    <form id="formulario_configuracion_notificaciones" method="post">
        <h2>Configuración de Notificaciones y Automatizaciones</h2>
        
        <label for="intervalo_notificaciones">Intervalo de notificaciones (en horas):</label>
        <input type="number" id="intervalo_notificaciones" name="intervalo_notificaciones" value="<?php echo get_option('intervalo_notificaciones', 24); ?>">

        <label for="habilitar_automatizaciones">Habilitar automatizaciones:</label>
        <input type="checkbox" id="habilitar_automatizaciones" name="habilitar_automatizaciones" <?php checked(get_option('habilitar_automatizaciones', 1)); ?>>

        <input type="submit" name="guardar_configuracion" value="Guardar Configuración">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('configuracion_notificaciones', 'formulario_configuracion_notificaciones');

// Guardar configuración de notificaciones y automatizaciones
function guardar_configuracion_notificaciones() {
    if (isset($_POST['guardar_configuracion'])) {
        update_option('intervalo_notificaciones', intval($_POST['intervalo_notificaciones']));
        update_option('habilitar_automatizaciones', isset($_POST['habilitar_automatizaciones']) ? 1 : 0);

        wp_clear_scheduled_hook('enviar_notificaciones_eventos_proximos');
        if (get_option('habilitar_automatizaciones', 1)) {
            wp_schedule_event(time(), 'hourly', 'enviar_notificaciones_eventos_proximos');
        }
    }
}
add_action('init', 'guardar_configuracion_notificaciones');

