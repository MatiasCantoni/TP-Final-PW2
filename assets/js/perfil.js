document.addEventListener('DOMContentLoaded', function() {
    
    if (typeof urlPerfil !== 'undefined' && document.getElementById("qrcode")) {
        new QRCode(document.getElementById("qrcode"), {
            text: urlPerfil,
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    document.querySelectorAll('.partida-fecha').forEach(function(elem) {
        const fechaTexto = elem.textContent.trim();
        if (fechaTexto && !fechaTexto.includes('/')) {
            const fecha = new Date(fechaTexto);
            if (!isNaN(fecha)) {
                elem.textContent = fecha.toLocaleDateString('es-AR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
    });
});