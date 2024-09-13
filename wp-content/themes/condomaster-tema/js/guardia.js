jQuery(document).ready(function($) {
    $('#view-all-tasks').on('click', function() {
        window.location.href = '/todas-las-tareas'; // Redirige a la página de todas las tareas
    });

    $('.task-actions button').on('click', function() {
        var action = $(this).data('action');
        var taskId = $(this).data('task-id');

        var formData = new FormData();
        formData.append('action', 'save_task_status');
        formData.append('task_id', taskId);
        formData.append('status', action);

        if (action === 'complete') {
            var evidence = $('#evidence')[0].files[0];
            formData.append('evidence', evidence);
        } else if (action === 'skip') {
            var reason = $('#skip-reason').val();
            formData.append('reason', reason);
        }

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert(response.data);
                }
            }
        });
    });
});
