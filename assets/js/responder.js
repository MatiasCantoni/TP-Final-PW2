let reportarBtn = document.getElementById("reportar-btn");
let enviarReporteBtn = document.getElementById("enviar-reporte-btn");
let mensajeReporte = document.getElementById("mensaje-reporte");

reportarBtn.addEventListener("click", function() {
    document.getElementById("reportar-form").style.display = "block";
});

enviarReporteBtn.addEventListener("click", function() {
    document.getElementById("reportar-form").style.display = "none";
    mensajeReporte.style.display = "block";
});