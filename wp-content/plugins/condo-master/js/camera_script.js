// camera_script.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript cargado y ejecutándose');

    // Simple prueba: Cambia el color de fondo de un elemento al presionar un botón
    var button = document.getElementById('test-button');
    if (button) {
        button.addEventListener('click', function() {
            var colorChangeElement = document.getElementById('color-change');
            if (colorChangeElement) {
                colorChangeElement.style.backgroundColor = 'lightblue';
            }
            var messageElement = document.getElementById('message');
            if (messageElement) {
                messageElement.innerText = '¡El botón ha sido presionado!';
            }
        });
    }
});
