let segundos = 11;
let intervaloRegresivo;

function iniciarCronometro() {

  intervaloRegresivo = setInterval(() => {
      if (segundos <= 0) {
        detenerCronometro();

        let form = document.getElementById('form-pregunta');
        let inputOpcionE = document.createElement('input');
        inputOpcionE.type = 'hidden';
        inputOpcionE.name = 'opcion';
        inputOpcionE.value = 'A';
        form.appendChild(inputOpcionE);
        // darle valor 1 al id_pregunta
        let inputIdPregunta = document.createElement('input');
        inputIdPregunta.type = 'hidden';
        inputIdPregunta.name = 'id_pregunta';
        inputIdPregunta.value = '1';
        form.appendChild(inputIdPregunta);
        let inputTiempoTerminado = document.createElement('input');
        inputTiempoTerminado.type = 'hidden';
        inputTiempoTerminado.name = 'tiempo_terminado';
        inputTiempoTerminado.value = '1';
        form.appendChild(inputTiempoTerminado);
        form.submit();  
        return;
      }
      if (segundos <= 6){
        document.getElementById('tiempo-restante').classList.add('tiempo-acabando');
      }
      segundos--;
      document.getElementById('tiempo-restante').innerHTML = segundos;
    }, 1000);
}

function detenerCronometro() {
  clearInterval(intervaloRegresivo);
  intervaloRegresivo = null;
}

iniciarCronometro();