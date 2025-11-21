google.charts.load('current', {'packages':['corechart']});

let chart;
let total = 0;

function dibujarGrafico(datos) {
    // Validamos que datos no sea nulo o vacio
    if (!datos || !Array.isArray(datos) || datos.length === 0) {
        document.getElementById('grafico').innerHTML = '<p>No hay datos disponibles</p>';
        document.getElementById("totalCentro").innerText = '';

        return;
    }

    const filtro = document.getElementById("filtro").value;

    let tituloColumna = "";
    switch (filtro) {
        case "pais": tituloColumna = "País"; break;
        case "sexo": tituloColumna = "Sexo"; break;
        case "edad": tituloColumna = "Edad"; break;
        default: tituloColumna = "Categoría";
    }
    // calculamos el total para despues ponerlo en el medio del grafico
    total = datos.reduce((acc, row) => acc + parseInt(row.cantidad), 0);

    document.getElementById("totalCentro").innerText = `Total: ${total}`;

    const data = new google.visualization.DataTable();
    data.addColumn('string', tituloColumna);
    data.addColumn('number', 'Usuarios');

    datos.forEach(row => {
        data.addRow([ row.etiqueta, parseInt(row.cantidad) ]);
    });

    const options = {
        title: `Jugadores por ${tituloColumna}`,
        pieHole: 0.4,
        chartArea: { 
            width: '80%', 
            height: '80%' 
        },
        legend: {
            position: 'right',
            textStyle: { fontSize: 14 }
        }
    };

    chart = new google.visualization.PieChart(
        document.getElementById('grafico')
    );
    
    chart.draw(data, options);
    posicionarTotalEnCentro();
}

// logica para exportar a pdf
document.getElementById("btnExportPDF").addEventListener("click", function () {
    if (!chart) {
        console.log('error');
    return;
    }

    // Accedemos a las variables globales definidas en la vista
    const jugadoresTotales = window.adminStats.jugadores;
    const partidasJugadas = window.adminStats.partidas;
    const preguntasTotales = window.adminStats.preguntas;
    const preguntasTotalesCreadas = window.adminStats.creadas;

    const imgUri = chart.getImageURI();

    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({ orientation: 'landscape'});

    pdf.setFontSize(16);
    const texto =  `Total: ${total}. \n
    ESTADISTICAS:
    Jugadores totales: ${jugadoresTotales}.
    Partidas jugadas: ${partidasJugadas}.
    Preguntas en el juego: ${preguntasTotales}.
    Preguntas creadas: ${preguntasTotalesCreadas}. `
                    
    pdf.addImage(imgUri, 'PNG', 50, 10, 270, 150);
    pdf.text(texto, 10, 140);
    // pdf.output('dataurlnewwindow'); en brave no funciona
    pdf.save("estadisticas.pdf");
});


function posicionarTotalEnCentro() {
    const svg = document.querySelector("#grafico svg");
    const totalDiv = document.getElementById("totalCentro");

    if (!svg) return;

    // Obtiene el "bounding box" del SVG (posición real del gráfico)
    const bbox = svg.getBoundingClientRect();
    const container = document.getElementById("chartContainer").getBoundingClientRect();

    // Calcula el centro real dentro del contenedor
    const centerX = bbox.left + bbox.width / 3.4 - container.left;
    const centerY = bbox.top + bbox.height / 2.2 - container.top;

    // Posiciona el total
    totalDiv.style.left = centerX + "px";
    totalDiv.style.top = centerY + "px";
    
}    

function actualizarGrafico(){
    const filtro = document.getElementById('filtro').value;
    const tiempo = document.getElementById('tiempo').value;

    console.log(`Obteniendo estadísticas: filtro=${filtro}, tiempo=${tiempo}`);

    fetch(`/admin/getEstadisticas?filtro=${filtro}&tiempo=${tiempo}`)
        .then(res => res.json())
        .then(data => {
            google.charts.setOnLoadCallback(function () {
                dibujarGrafico(data);
            });
        });
}

google.charts.setOnLoadCallback(actualizarGrafico);

document.getElementById('filtro').addEventListener('change', actualizarGrafico);
document.getElementById('tiempo').addEventListener('change', actualizarGrafico);
window.addEventListener('resize', actualizarGrafico);

window.addEventListener('resize', () => {
    if(chart) {
        actualizarGrafico();
    }
});

// const btnQuitarPermisos = document.getElementById("btnQuitarPermisos");


const btnDarPermisos = document.getElementById("btnDarPermisos");
const btnQuitarPermisos = document.getElementById("btnQuitarPermisos");
btnDarPermisos.disabled = true;
btnQuitarPermisos.disabled = true;

function darPermisos(){
    let nombreUsuario = document.getElementById("usuario").value;
    let mensajeUsuario = document.getElementById("mensajeUsuario");

    
    fetch(`/admin/darPermisos?usuario=${nombreUsuario}`)
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            mensajeUsuario.textContent = 'Éxito: ' + data.message;
            mensajeUsuario.style.color = 'green';
        } else {
            mensajeUsuario.textContent = 'Error: ' + data.message;
            mensajeUsuario.style.color = 'red';
        }
    });
}

function quitarPermisos(){
    let nombreUsuario = document.getElementById("usuario").value;
    let mensajeUsuario = document.getElementById("mensajeUsuario");

    
    fetch(`/admin/quitarPermisos?usuario=${nombreUsuario}`)
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            mensajeUsuario.textContent = 'Éxito: ' + data.message;
            mensajeUsuario.style.color = 'green';
        } else {
            mensajeUsuario.textContent = 'Error: ' + data.message;
            mensajeUsuario.style.color = 'red';
        }
    });
}
btnQuitarPermisos.addEventListener("click", quitarPermisos);
btnDarPermisos.addEventListener("click", darPermisos);
document.getElementById("usuario").addEventListener("keyup", function(){
    let nombreUsuario = document.getElementById("usuario").value;
    btnDarPermisos.disabled = (nombreUsuario == '') ? true : false;
    btnQuitarPermisos.disabled = (nombreUsuario == '') ? true : false;

});