document.addEventListener('DOMContentLoaded', function(){
    // Soporta ambas vistas: primera y segunda validación
    const form = document.querySelector('form[action="/user/segundaValidacionToken"], form[action="/user/primeraValidacionToken"]');
    const spinner = document.getElementById('validacionSpinner');

    if (form) {
        // Buscar el botón de submit dentro del propio form para evitar conflictos
        const btn = form.querySelector('button[type="submit"]') || document.getElementById('validarBtn');

        form.addEventListener('submit', function(e){
            // Mostrar spinner si existe y deshabilitar el botón para evitar envíos múltiples
            if (spinner) {
                spinner.style.display = 'flex';
            }
            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.7';
            }
        });
    }
});
