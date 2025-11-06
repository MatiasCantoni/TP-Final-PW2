// Obtener el canvas y el contexto
const canvas = document.getElementById("ruleta");
const ctx = canvas.getContext("2d");

// Configurar categorías (podés traerlas dinámicamente desde Mustache)
const categorias = ["Historia", "Ciencia", "Deportes", "Arte", "Geografia", "Entretenimiento"];
const numCategorias = categorias.length;
const anguloPorSector = (2 * Math.PI) / numCategorias;

// Variables de animación
let anguloActual = 0;
let girando = false;
let velocidad = 0;
let categoriaSeleccionada = null;

// Colores alternados
const colores = ["#ffcc00", "#ff6666", "#66ccff", "#66ff99", "#cc99ff", "#ff9966"];

// Dibujar la ruleta
function dibujarRuleta() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  const radio = canvas.width / 2;

  for (let i = 0; i < numCategorias; i++) {
    const inicio = anguloActual + i * anguloPorSector;
    const fin = inicio + anguloPorSector;

    // Dibujar sector
    ctx.beginPath();
    ctx.moveTo(radio, radio);
    ctx.arc(radio, radio, radio, inicio, fin);
    ctx.fillStyle = colores[i % colores.length];
    ctx.fill();
    ctx.stroke();

    // Escribir texto
    ctx.save();
    ctx.translate(radio, radio);
    ctx.rotate(inicio + anguloPorSector / 2);
    ctx.textAlign = "right";
    ctx.fillStyle = "#000";
    ctx.font = "16px Arial";
    ctx.fillText(categorias[i], radio - 10, 10);
    ctx.restore();
  }

  // Flecha indicadora (arriba)
    ctx.fillStyle = "#000";
    ctx.beginPath();
    ctx.moveTo(radio - 10, 0);
    ctx.lineTo(radio + 10, 0);
    ctx.lineTo(radio, 20);
    ctx.closePath();
    ctx.fill();
    
}

// Animar el giro
function animar() {
  if (!girando) return;
  velocidad *= 0.98; // desaceleración
  if (velocidad <= 0.01) {
    girando = false;
    velocidad = 0;
    determinarCategoria();
    return;
  }
  anguloActual += velocidad;
  anguloActual %= 2 * Math.PI;
  dibujarRuleta();
  requestAnimationFrame(animar);
}

// Determinar la categoría donde cayó
function determinarCategoria() {
  // Calculamos el ángulo relativo entre el puntero y la rotación actual
  // y a partir de eso determinamos el índice del sector.
  const pointerAngle = -Math.PI / 2; // arriba
  const twoPi = 2 * Math.PI;
  // Ángulo relativo desde la ruleta hacia el puntero
  let relative = (pointerAngle - anguloActual) % twoPi;
  if (relative < 0) relative += twoPi;
  const sector = Math.floor(relative / anguloPorSector) % numCategorias;
  categoriaSeleccionada = categorias[sector];
  // Mostrar la categoría en el texto y en el modal
  document.getElementById("modal-categoria-texto").innerHTML = `<strong>${categoriaSeleccionada}</strong>`;
  
  // Mostrar el modal usando Bootstrap y evitar que se cierre al hacer click fuera o presionar Esc
  const categoriaModal = new bootstrap.Modal(document.getElementById('categoriaModal'), {
    backdrop: 'static', // evita cierre al clickear fuera
    keyboard: false     // evita cierre con la tecla Esc
  });
  categoriaModal.show();

  // Redirigir a la pregunta correspondiente cuando se acepta
  document.getElementById("aceptar-categoria").onclick = () => {
    window.location.href = `pregunta?categoria=${encodeURIComponent(categoriaSeleccionada)}`;
  };
}

// Evento del botón "Girar"
document.getElementById("girar").addEventListener("click", () => {
  if (girando) return;
  velocidad = Math.random() * 0.3 + 0.25; // velocidad inicial aleatoria
  girando = true;
  animar();
  let botonGirar = document.getElementById("girar");
  botonGirar.disabled = true;
  botonGirar.style.cursor = "not-allowed";
  botonGirar.style.opacity = "0.5";
});

// Dibujar ruleta inicial
dibujarRuleta();
