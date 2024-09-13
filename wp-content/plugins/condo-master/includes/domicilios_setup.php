<?php
// Función para mostrar la configuración del tipo de fraccionamiento y campos de domicilio
function domicilios_setup_page() {
    ?>
    <div class="wrap">
        <h1>Configuración de Domicilios</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('domicilios_options_group');
            do_settings_sections('domicilios_setup');
            submit_button();
            ?>
        </form>
        <?php
        // Mostrar el formulario de registro manual de usuarios si es necesario
        custom_user_registration_form();
        ?>
    </div>
    <?php
}

// Configuración de opciones para domicilios
function domicilios_setup_settings() {
    register_setting('domicilios_options_group', 'domicilios_config');

    add_settings_section(
        'domicilios_main_section',
        'Configuración Principal',
        null,
        'domicilios_setup'
    );

    add_settings_field(
        'tipo_fraccionamiento',
        'Tipo de Fraccionamiento',
        'domicilios_tipo_callback',
        'domicilios_setup',
        'domicilios_main_section'
    );

    // Agregar campos de dirección adicionales según el tipo de fraccionamiento
    add_settings_field(
        'direccion_adicional',
        'Configuración de Dirección Adicional',
        'domicilios_direccion_callback',
        'domicilios_setup',
        'domicilios_main_section'
    );
}
add_action('admin_init', 'domicilios_setup_settings');

// Callback para la selección de tipo de fraccionamiento
function domicilios_tipo_callback() {
    $config = get_option('domicilios_config', array());
    $tipos = isset($config['tipo']) ? (array)$config['tipo'] : array(); // Asegurarse de que 'tipo' sea un array
    ?>
    <label><input type="checkbox" name="domicilios_config[tipo][]" value="residencial" <?php checked(in_array('residencial', $tipos)); ?> onclick="toggleFields();" /> Residencial</label><br />
    <label><input type="checkbox" name="domicilios_config[tipo][]" value="industrial" <?php checked(in_array('industrial', $tipos)); ?> onclick="toggleFields();" /> Industrial</label><br />
    <label><input type="checkbox" name="domicilios_config[tipo][]" value="comercial" <?php checked(in_array('comercial', $tipos)); ?> onclick="toggleFields();" /> Comercial</label><br />
    <?php
}

// Callback para los campos adicionales de dirección
function domicilios_direccion_callback() {
    $config = get_option('domicilios_config', array());
    $tipos = isset($config['tipo']) ? (array)$config['tipo'] : array(); // Asegurarse de que 'tipo' sea un array

    // Definir los campos para cada tipo de fraccionamiento
    $campos_residencial = array(
        'edificio' => 'Edificio',
        'piso' => 'Piso',
        'torre' => 'Torre',
        'apartamento' => 'Apartamento',
        'bloque' => 'Bloque',
        'unidad' => 'Unidad',
    );
    
    $campos_industrial = array(
        'bodega' => 'Bodega',
        'nave' => 'Nave',
        'anden' => 'Andén',
        'puerto' => 'Puerto',
        'oficina' => 'Oficina',
        'maquina' => 'Máquina',
    );

    $campos_comercial = array(
        'calle' => 'Calle',
        'local' => 'Local',
        'oficina' => 'Oficina',
        'plaza' => 'Plaza',
        'tienda' => 'Tienda',
        'zona' => 'Zona',
    );
    
    // Mostrar campos según el tipo de fraccionamiento
    ?>
    <div class="custom-fields-container">
        <div class="custom-fields-column">
            <h2>Residencial</h2>
            <?php foreach ($campos_residencial as $key => $label) : ?>
                <label><input type="checkbox" name="domicilios_config[<?php echo esc_attr($key); ?>]" value="1" <?php checked(1, isset($config[$key])); ?> /> <?php echo esc_html($label); ?></label><br />
            <?php endforeach; ?>
        </div>

        <div class="custom-fields-column">
            <h2>Industrial</h2>
            <?php foreach ($campos_industrial as $key => $label) : ?>
                <label><input type="checkbox" name="domicilios_config[<?php echo esc_attr($key); ?>]" value="1" <?php checked(1, isset($config[$key])); ?> /> <?php echo esc_html($label); ?></label><br />
            <?php endforeach; ?>
        </div>

        <div class="custom-fields-column">
            <h2>Comercial</h2>
            <?php foreach ($campos_comercial as $key => $label) : ?>
                <label><input type="checkbox" name="domicilios_config[<?php echo esc_attr($key); ?>]" value="1" <?php checked(1, isset($config[$key])); ?> /> <?php echo esc_html($label); ?></label><br />
            <?php endforeach; ?>
        </div>
    </div>

    <style>
    .custom-fields-container {
        display: flex;
        gap: 20px;
    }
    .custom-fields-column {
        flex: 1;
        min-width: 200px;
    }
    </style>

    <script>
    function toggleFields() {
        // Obtener los tipos seleccionados
        const tipos = [];
        document.querySelectorAll('input[name="domicilios_config[tipo][]"]:checked').forEach(el => {
            tipos.push(el.value);
        });

        // Mostrar u ocultar campos según los tipos seleccionados
        document.querySelectorAll('.custom-fields-column').forEach(column => {
            const columnType = column.querySelector('h2').textContent.toLowerCase();
            if (tipos.includes(columnType)) {
                column.style.display = 'block';
            } else {
                column.style.display = 'none';
            }
        });
    }

    // Inicializar el estado de los campos en función del valor actual
    document.addEventListener('DOMContentLoaded', function() {
        toggleFields();
    });
    </script>
    <?php
}

// Página de configuración para campos adicionales
function domicilios_custom_fields_page() {
    ?>
    <div class="wrap">
        <h1>Configuración de Campos Adicionales</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('domicilios_custom_fields_group');
            do_settings_sections('domicilios_custom_fields_page');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Configuración de campos adicionales
function domicilios_custom_fields_settings() {
    register_setting('domicilios_custom_fields_group', 'domicilios_custom_fields');

    add_settings_section(
        'domicilios_custom_fields_section',
        'Campos Personalizados',
        null,
        'domicilios_custom_fields_page'
    );

    add_settings_field(
        'custom_fields',
        'Campos Personalizados',
        'domicilios_custom_fields_callback',
        'domicilios_custom_fields_page',
        'domicilios_custom_fields_section'
    );
}
add_action('admin_init', 'domicilios_custom_fields_settings');

// Callback para mostrar campos personalizados
function domicilios_custom_fields_callback() {
    $fields = get_option('domicilios_custom_fields', array());
    ?>
    <table class="form-table">
        <tbody>
            <?php foreach ($fields as $key => $field) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($field['label']); ?></th>
                    <td><input type="text" name="domicilios_custom_fields[<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($field['label']); ?>" /></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="button" id="add-field">Añadir Campo</button>
    <script>
    document.getElementById('add-field').addEventListener('click', function() {
        var tbody = document.querySelector('.form-table tbody');
        var rowCount = tbody.rows.length;
        var newRow = document.createElement('tr');
        newRow.innerHTML = '<th scope="row">Campo ' + (rowCount + 1) + '</th><td><input type="text" name="domicilios_custom_fields[new' + rowCount + '][label]" value="" /></td>';
        tbody.appendChild(newRow);
    });
    </script>
    <?php
}
