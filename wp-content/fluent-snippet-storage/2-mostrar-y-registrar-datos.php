<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: Mostrar y Registrar Datos de Paquetería y Visitas
* @type: php_content
* @status: draft
* @created_by: 
* @created_at: 
* @updated_at: 2024-08-29 21:55:29
* @is_valid: 
* @updated_by: 
* @priority: 10
* @run_at: shortcode
* @load_as_file: 
* @condition: {"status":"no","run_if":"assertive","items":[[]]}
*/
?>
<?php if (!defined("ABSPATH")) { return;} // <Internal Doc End> ?>
// Asegúrate de que este código se ejecuta en el contexto adecuado de WordPress.

function mostrar_datos_paqueteria() {
    global $wpdb;

    // Nombre de la tabla de paquetería
    $table_name = $wpdb->prefix . 'control_accesos_paqueteria';

    // Recupera los datos de la tabla
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Verifica si hay resultados
    if (!empty($results)) {
        echo '<table>
                <thead>
                    <tr>
                        <th>Nombre del Paquete</th>
                        <th>Número de Tracking</th>
                        <th>Carrier</th>
                        <th>Fecha de Recepción</th>
                        <th>Dispositivo</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($results as $row) {
            echo '<tr>
                    <td>' . esc_html($row->nombre_paquete) . '</td>
                    <td>' . esc_html($row->numero_tracking) . '</td>
                    <td>' . esc_html($row->carrier) . '</td>
                    <td>' . esc_html($row->fecha_recepcion) . '</td>
                    <td>' . esc_html($row->dispositivo) . '</td>
                  </tr>';
        }
        echo '</tbody>
            </table>';
    } else {
        echo '<p>No hay datos de paquetería registrados.</p>';
    }
}

function mostrar_datos_visitas() {
    global $wpdb;

    // Nombre de la tabla de visitas
    $table_name = $wpdb->prefix . 'control_accesos_visitas';

    // Recupera los datos de la tabla
    $results = $wpdb->get_results("SELECT * FROM $table_name");

    // Verifica si hay resultados
    if (!empty($results)) {
        echo '<table>
                <thead>
                    <tr>
                        <th>Subcondominio</th>
                        <th>Nombre del Residente</th>
                        <th>Número de Personas</th>
                        <th>Notas</th>
                        <th>Fecha</th>
                        <th>Dispositivo</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($results as $row) {
            echo '<tr>
                    <td>' . esc_html($row->subcondominio) . '</td>
                    <td>' . esc_html($row->nombre_residente) . '</td>
                    <td>' . esc_html($row->numero_personas) . '</td>
                    <td>' . esc_html($row->notas) . '</td>
                    <td>' . esc_html($row->fecha) . '</td>
                    <td>' . esc_html($row->dispositivo) . '</td>
                  </tr>';
        }
        echo '</tbody>
            </table>';
    } else {
        echo '<p>No hay registros de visitas.</p>';
    }
}

function registrar_paqueteria() {
    if (isset($_POST['submit_paqueteria'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'control_accesos_paqueteria';
        $wpdb->insert(
            $table_name,
            array(
                'nombre_paquete' => sanitize_text_field($_POST['nombre-paquete']),
                'numero_tracking' => sanitize_text_field($_POST['numero-tracking']),
                'carrier' => sanitize_text_field($_POST['carrier']),
                'dispositivo' => sanitize_text_field($_POST['dispositivo']),
            )
        );
        echo '<p>Paquete registrado exitosamente.</p>';
    }
}

function registrar_visitas() {
    if (isset($_POST['submit_visitas'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'control_accesos_visitas';
        $wpdb->insert(
            $table_name,
            array(
                'subcondominio' => sanitize_text_field($_POST['subcondominio']),
                'nombre_residente' => sanitize_text_field($_POST['nombre-residente']),
                'numero_personas' => intval($_POST['numero-personas']),
                'notas' => sanitize_textarea_field($_POST['notas']),
                'dispositivo' => sanitize_text_field($_POST['dispositivo']),
            )
        );
        echo '<p>Visita registrada exitosamente.</p>';
    }
}

function obtener_subcondominios() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'control_accesos_subcondominios';
    return $wpdb->get_results("SELECT nombre FROM $table_name");
}

function obtener_carriers() {
    // Lista de carriers predefinidos
    return array(
        'Carrier 1',
        'Carrier 2',
        'Carrier 3',
        'Carrier 4',
        'Carrier 5'
    );
}

function shortcode_control_accesos() {
    ob_start();

    // Registrar paquetería
    registrar_paqueteria();

    // Registrar visitas
    registrar_visitas();

    ?>
    <div id="control-accesos">
        <h1>Control de Accesos y Paquetería</h1>
        <!-- Menú de pestañas -->
        <div class="tabs">
            <button class="tablink" onclick="openTab(event, 'paqueteria')">Paquetería</button>
            <button class="tablink" onclick="openTab(event, 'visitas')">Visitas y Servicios</button>
        </div>
        <!-- Contenido de Paquetería -->
        <div id="paqueteria" class="tabcontent">
            <h2>Registro de Paquetería</h2>
            <form id="registro-paqueteria" method="post">
                <label for="nombre-paquete">Nombre del Paquete:</label>
                <input type="text" id="nombre-paquete" name="nombre-paquete" required>

                <label for="numero-tracking">Número de Tracking:</label>
                <input type="text" id="numero-tracking" name="numero-tracking" required>

                <label for="carrier">Carrier:</label>
                <select id="carrier" name="carrier" required>
                    <?php
                    $carriers = obtener_carriers();
                    foreach ($carriers as $carrier) {
                        echo '<option value="' . esc_attr($carrier) . '">' . esc_html($carrier) . '</option>';
                    }
                    ?>
                </select>

                <input type="hidden" name="dispositivo" value="<?php echo esc_attr($_SERVER['HTTP_USER_AGENT']); ?>">

                <input type="submit" name="submit_paqueteria" value="Registrar Paquete">
            </form>

            <?php mostrar_datos_paqueteria(); ?>
        </div>

        <!-- Contenido de Visitas y Servicios -->
        <div id="visitas" class="tabcontent">
            <h2>Registro de Visitas y Servicios</h2>
            <form id="registro-visitas" method="post">
                <label for="subcondominio">Subcondominio:</label>
                <select id="subcondominio" name="subcondominio" required>
                    <?php
                    $subcondominios = obtener_subcondominios();
                    foreach ($subcondominios as $subcondominio) {
                        echo '<option value="' . esc_attr($subcondominio->nombre) . '">' . esc_html($subcondominio->nombre) . '</option>';
                    }
                    ?>
                </select>

                <label for="nombre-residente">Nombre del Residente:</label>
                <input type="text" id="nombre-residente" name="nombre-residente" required>

                <label for="numero-personas">Número de Personas:</label>
                <input type="number" id="numero-personas" name="numero-personas" required>

                <label for="notas">Notas:</label>
                <textarea id="notas" name="notas"></textarea>

                <input type="hidden" name="dispositivo" value="<?php echo esc_attr($_SERVER['HTTP_USER_AGENT']); ?>">

                <input type="submit" name="submit_visitas" value="Registrar Visita">
            </form>

            <?php mostrar_datos_visitas(); ?>
        </div>
    </div>
    <script>
        function openTab(evt, tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
                          }
               tablinks = document.getElementsByClassName("tablink");
               for (i = 0; i < tablinks.length; i++) {
                   tablinks[i].className = tablinks[i].className.replace(" active", "");
               }
               document.getElementById(tabName).style.display = "block";
               evt.currentTarget.className += " active";
           }
           // Abre la primera pestaña por defecto
           document.getElementsByClassName("tablink")[0].click();
       </script>
       <style>
           /* Estilos para las pestañas */
           .tab {
               overflow: hidden;
               border: 1px solid #ccc;
               background-color: #f1f1f1;
           }
           .tab button {
               background-color: inherit;
               border: none;
               outline: none;
               cursor: pointer;
               padding: 14px 16px;
               transition: 0.3s;
           }
           .tab button.active {
               background-color: #ddd;
           }
           .tabcontent {
               display: none;
               padding: 6px 12px;
               border: 1px solid #ccc;
               border-top: none;
           }
           .tabcontent table {
               width: 100%;
               border-collapse: collapse;
           }
           .tabcontent table, .tabcontent th, .tabcontent td {
               border: 1px solid #ddd;
           }
           .tabcontent th, .tabcontent td {
               padding: 8px;
               text-align: left;
           }
           .tabcontent tr:nth-child(even) {
               background-color: #f2f2f2;
           }
           .tabcontent tr:hover {
               background-color: #ddd;
           }
       </style>
       <?php
       return ob_get_clean();
   }

   // Registrar el shortcode
   add_shortcode('control_accesos', 'shortcode_control_accesos');

