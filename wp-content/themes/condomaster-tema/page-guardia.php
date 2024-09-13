<?php
/* Template Name: Guardia Dashboard */
get_header(); // Incluye el encabezado del tema
?>

<div id="guardia-dashboard">
    <!-- Código HTML para el tablero del guardia -->
    <h1>Dashboard del Guardia</h1>
    
    <!-- Sección para mostrar las próximas tareas -->
    <div id="proximas-tareas">
        <h2>Próximas Tareas</h2>
        <!-- Aquí puedes usar un bucle para mostrar las tareas -->
    </div>

    <!-- Botón para ver todas las tareas -->
    <a href="<?php echo esc_url(get_permalink(get_page_by_title('Todas las Tareas'))); ?>" class="button">Ver Todas las Tareas</a>

    <!-- Instructivo para las tareas -->
    <div id="instructivo-tarea">
        <!-- Aquí se mostrará el instructivo de la tarea seleccionada -->
    </div>

    <!-- Código para el botón de iniciar/terminar tarea y tomar evidencia -->
    <div id="control-tareas">
        <button id="btn-iniciar-tarea">Iniciar Tarea</button>
        <button id="btn-finalizar-tarea">Finalizar Tarea</button>
    </div>

    <!-- Área para tomar evidencia fotográfica -->
    <div id="evidencia-fotografica">
        <!-- Botones y opciones para tomar o saltar evidencia -->
        <button id="btn-tomar-evidencia">Tomar Evidencia</button>
        <button id="btn-saltar-evidencia">Saltar Evidencia</button>
    </div>

    <!-- Selector de opciones al omitir una tarea -->
    <div id="opciones-omitir-tarea">
        <!-- Aquí se puede mostrar un selector con razones para omitir -->
        <select id="omitir-tarea">
            <option value="realizada-por-otro">Alguien más realizó la tarea</option>
            <option value="no-aplica">No aplica</option>
            <option value="falta-personal">Falta de personal</option>
            <!-- Agregar más opciones según sea necesario -->
        </select>
    </div>
</div>

<?php
get_footer(); // Incluye el pie de página del tema
?>
