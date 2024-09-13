<?php
// Evitar acceso directo
if (!defined('ABSPATH')) {
    exit;
}

// Mostrar formulario para añadir o editar persona
function mostrar_formulario_persona($id = 0) {
    $persona = $id ? obtener_persona_por_id($id) : array('nombre' => '', 'telefono' => '', 'domicilio' => '');
    
    if (is_wp_error($persona)) {
        echo '<p>' . $persona->get_error_message() . '</p>';
        return;
    }

    ?>
    <form method="post" action="">
        <input type="hidden" name="persona_id" value="<?php echo esc_attr($id); ?>" />
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo esc_attr($persona->nombre); ?>" required />
        <br />
        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo esc_attr($persona->telefono); ?>" />
        <br />
        <label for="domicilio">Domicilio:</label>
        <input type="text" name="domicilio" value="<?php echo esc_attr($persona->domicilio); ?>" />
        <br />
        <input type="submit" name="guardar_persona" value="<?php echo $id ? 'Actualizar' : 'Guardar'; ?>" />
    </form>
    <?php
}

// Procesar el formulario de añadir o editar persona
function procesar_formulario_persona() {
    if (isset($_POST['guardar_persona'])) {
        $id = isset($_POST['persona_id']) ? intval($_POST['persona_id']) : 0;
        $data = array(
            'nombre' => sanitize_text_field($_POST['nombre']),
            'telefono' => sanitize_text_field($_POST['telefono']),
            'domicilio' => sanitize_text_field($_POST['domicilio']),
        );

        if ($id) {
            $resultado = actualizar_persona($id, $data);
            $mensaje = $resultado !== false ? 'Persona actualizada con éxito.' : 'Error al actualizar la persona.';
        } else {
            $resultado = agregar_persona($data);
            $mensaje = !is_wp_error($resultado) ? 'Persona agregada con éxito.' : 'Error al agregar la persona.';
        }

        echo '<p>' . esc_html($mensaje) . '</p>';
    }
}
add_action('wp', 'procesar_formulario_persona');

// Mostrar el formulario en el front-end
function mostrar_formulario_persona_frontend($id = 0) {
    if (current_user_can('manage_options')) { // Verifica si el usuario tiene permisos para gestionar opciones
        mostrar_formulario_persona($id);
    } else {
        echo '<p>No tienes permiso para acceder a este formulario.</p>';
    }
}
?>
