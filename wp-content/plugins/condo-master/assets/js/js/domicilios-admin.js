jQuery(document).ready(function($) {
    function toggleFields(tipo) {
        $('#residencial-fields').hide();
        $('#industrial-fields').hide();
        $('#comercial-fields').hide();

        if (tipo === 'residencial') {
            $('#residencial-fields').show();
        } else if (tipo === 'industrial') {
            $('#industrial-fields').show();
        } else if (tipo === 'comercial') {
            $('#comercial-fields').show();
        }
    }

    var tipoSeleccionado = $('input[name="domicilios_options[tipo]"]:checked').val();
    if (tipoSeleccionado) {
        toggleFields(tipoSeleccionado);
    }

    $('input[name="domicilios_options[tipo]"]').on('change', function() {
        var tipo = $(this).val();
        toggleFields(tipo);
    });
});
