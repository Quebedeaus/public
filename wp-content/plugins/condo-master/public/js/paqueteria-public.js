jQuery(document).ready(function($) {
    function updatePaqueteriaInfo() {
        $.ajax({
            url: paqueteria_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'paqueteria_update',
                nonce: paqueteria_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.paqueteria-info').html(response.data.html);
                } else {
                    console.error('Error updating paqueteria info:', response.data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
            }
        });
    }

    // Update every 60 seconds
    setInterval(updatePaqueteriaInfo, 60000);

    // Handle filter form submission
    $(document).on('submit', '#paqueteria-filter-form', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var currentUrl = window.location.href.split('?')[0];
        var newUrl = currentUrl + '?' + formData;
        window.history.pushState({}, '', newUrl);
        updatePaqueteriaInfo();
    });

    // Handle sorting links
    $(document).on('click', '.paqueteria-table th a', function(e) {
        e.preventDefault();
        var newUrl = $(this).attr('href');
        window.history.pushState({}, '', newUrl);
        updatePaqueteriaInfo();
    });
});