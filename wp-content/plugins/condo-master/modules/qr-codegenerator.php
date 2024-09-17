<?php
// QR Code Generator Module

if (!defined('ABSPATH')) {
    exit;
}

function qr_generator_admin_page() {
    ?>
    <div class="wrap">
        <h1>Generador de Códigos QR</h1>
        <form method="post" action="">
            <?php
            if (isset($_POST['generate_qr_code'])) {
                check_admin_referer('generate_qr_code_nonce');
                
                $data = sanitize_text_field($_POST['qr_data']);
                $qr_code_url = qr_generate_code($data);
                echo '<h2>Código QR Generado:</h2>';
                echo '<img src="' . esc_url($qr_code_url) . '" alt="Código QR" />';
            }
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Datos para el Código QR</th>
                    <td>
                        <input type="text" name="qr_data" value="" />
                        <p class="description">Introduce los datos que deseas codificar en el código QR.</p>
                    </td>
                </tr>
            </table>
            <?php wp_nonce_field('generate_qr_code_nonce'); ?>
            <input type="submit" name="generate_qr_code" class="button button-primary" value="Generar Código QR" />
        </form>
    </div>
    <?php
}

function qr_generate_code($data) {
    // Utiliza una API de generación de códigos QR
    $api_url = 'https://api.qrserver.com/v1/create-qr-code/';
    $url = $api_url . '?data=' . urlencode($data) . '&size=150x150';
    return $url;
}
?>
