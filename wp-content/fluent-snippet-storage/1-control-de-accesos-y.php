<?php
// <Internal Doc Start>
/*
*
* @description: 
* @tags: 
* @group: 
* @name: Control de accesos y paqueteria
* @type: php_content
* @status: draft
* @created_by: 
* @created_at: 
* @updated_at: 2024-08-29 01:45:13
* @is_valid: 
* @updated_by: 
* @priority: 10
* @run_at: shortcode
* @load_as_file: 
* @condition: {"status":"no","run_if":"assertive","items":[[]]}
*/
?>
<?php if (!defined("ABSPATH")) { return;} // <Internal Doc End> ?>
function control_accesos_shortcode() {
    ob_start();
    ?>
    <div class="control-accesos">
        <h2>Control de Accesos y Paquetería Residencial Cuyagua</h2>
        
        <form method="post">
            <fieldset>
                <legend>Registro de Paquetería</legend>
                <label for="nombre_residente">Nombre del Residente:</label>
                <input type="text" id="nombre_residente" name="nombre_residente" required>
                
                <label for="numero_residencia">Número de Residencia:</label>
                <input type="text" id="numero_residencia" name="numero_residencia" required>
                
                <label for="guardia_recepcion">Guardia que Recibe:</label>
                <input type="text" id="guardia_recepcion" name="guardia_recepcion" required>
                
                <label for="notas">Notas:</label>
                <textarea id="notas" name="notas"></textarea>
                
                <button type="submit">Registrar Paquete</button>
            </fieldset>
        </form>
        
        <!-- Sección de entrega de paquetes -->
        <form method="post">
            <fieldset>
                <legend>Entrega de Paquetes al Residente</legend>
                <label for="nombre_residente_entrega">Nombre del Residente:</label>
                <input type="text" id="nombre_residente_entrega" name="nombre_residente_entrega" required>

                <label for="guardia_entrega">Guardia que Entrega:</label>
                <input type="text" id="guardia_entrega" name="guardia_entrega" required>

                <label for="numero_paquete">Número del Paquete:</label>
                <input type="text" id="numero_paquete" name="numero_paquete" required>

                <button type="submit">Registrar Entrega</button>
            </fieldset>
        </form>
    </div>

    <style>
    .control-accesos {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background: #f7f7f7;
        border-radius: 8px;
    }

    fieldset {
        border: 1px solid #ccc;
        margin-bottom: 20px;
        padding: 10px;
    }

    legend {
        font-weight: bold;
    }

    label {
        display: block;
        margin-top: 10px;
    }

    input[type="text"], textarea {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
        box-sizing: border-box;
    }

    button {
        margin-top: 10px;
        padding: 10px 15px;
        background-color: #0073aa;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #005a87;
    }

    @media screen and (max-width: 600px) {
        .control-accesos {
            padding: 10px;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}
add_shortcode('control_accesos', 'control_accesos_shortcode');
