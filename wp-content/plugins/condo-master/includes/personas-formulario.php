<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

function mostrar_formulario_persona($persona = null) {
    ?>
    <div class="wrap">
        <h1><?php echo $persona ? 'Editar Persona' : 'Agregar Nueva Persona'; ?></h1>
        <form method="post" action="">
            <?php
            // Añadir nonce para seguridad
            wp_nonce_field('guardar_persona', 'persona_nonce');
            ?>
            <table class="form-table">
                <tr>
                    <th><label for="nombre">Nombre</label></th>
                    <td><input type="text" name="nombre" id="nombre" value="<?php echo esc_attr($persona ? $persona->nombre : ''); ?>" required /></td>
                </tr>
                <tr>
                    <th><label for="telefono">Teléfono</label></th>
                    <td><input type="text" name="telefono" id="telefono" value="<?php echo esc_attr($persona ? $persona->telefono : ''); ?>" /></td>
                </tr>
                <tr>
                    <th><label for="domicilio">Domicilio</label></th>
                    <td><input type="text" name="domicilio" id="domicilio" value="<?php echo esc_attr($persona ? $persona->domicilio : ''); ?>" /></td>
                </tr>
            </table>
            <?php submit_button($persona ? 'Actualizar Persona' : 'Agregar Persona'); ?>
            <input type="hidden" name="action" value="<?php echo $persona ? 'editar_persona' : 'agregar_persona'; ?>" />
            <?php if ($persona): ?>
                <input type="hidden" name="persona_id" value="<?php echo esc_attr($persona->id); ?>" />
            <?php endif; ?>
        </form>
    </div>
    <?php
}

// Procesar formulario de agregar/editar persona
function procesar_formulario_persona() {
    if (isset($_POST['persona_nonce']) && wp_verify_nonce($_POST['persona_nonce'], 'guardar_persona')) {
        $action = isset($_POST['action']) ? $_POST['action'] : '';
        $nombre = sanitize_text_field($_POST['nombre']);
        $telefono = sanitize_text_field($_POST['telefono']);
        $domicilio = sanitize_text_field($_POST['domicilio']);
        
        $data = array(
            'nombre' => $nombre,
            'telefono' => $telefono,
            'domicilio' => $domicilio
        );

        if ($action === 'agregar_persona') {
            $resultado = agregar_persona($data);
            if (is_wp_error($resultado)) {
                wp_die($resultado->get_error_message());
            }
            wp_redirect(add_query_arg('mensaje', 'persona_agregada', $_SERVER['HTTP_REFERER']));
            exit;
        } elseif ($action === 'editar_persona') {
            $id = isset($_POST['persona_id']) ? intval($_POST['persona_id']) : 0;
            $resultado = actualizar_persona($id, $data);
            if (is_wp_error($resultado)) {
                wp_die($resultado->get_error_message());
            }
            wp_redirect(add_query_arg('mensaje', 'persona_actualizada', $_SERVER['HTTP_REFERER']));
            exit;
        }
    }
}

// Mostrar mensaje de éxito
function mostrar_mensaje_persona() {
    if (isset($_GET['mensaje'])) {
        $mensaje = $_GET['mensaje'];
        if ($mensaje === 'persona_agregada') {
            echo '<div class="notice notice-success is-dismissible"><p>Persona agregada exitosamente.</p></div>';
        } elseif ($mensaje === 'persona_actualizada') {
            echo '<div class="notice notice-success is-dismissible"><p>Persona actualizada exitosamente.</p></div>';
        }
    }
}

// Registrar las funciones para ser usadas en el back-end
add_action('admin_menu', function() {
    add_menu_page(
        'Gestión de Personas',
        'Gestión de Personas',
        'manage_options',
        'gestion_personas',
        'mostrar_formulario_persona'
    );
});

add_action('admin_init', 'procesar_formulario_persona');
add_action('admin_notices', 'mostrar_mensaje_persona');
?>
