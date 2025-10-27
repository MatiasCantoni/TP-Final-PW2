<?php

class GameController{
    private $model;
    private $renderer;

    public function __construct($model, $renderer)
    {
        $this->model = $model;
        $this->renderer = $renderer;
    }
    public function singleplayer(){
        if (!isset($_SESSION["usuario"])) {
            header("Location: index.php?controller=User&method=loginForm");
            exit();
        }

        $categorias = $this->model->getCategorias();
        $this->renderer->render("singleplayer", ["isSingleplayer" => true, "categorias" => $categorias]);
        
    }

    public function pregunta(){
        $categoria = $_GET["categoria"];
        $pregunta = $this->model->getPreguntaRandom($categoria);
        $this->renderer->render("pregunta", ["pregunta" => $pregunta]);
    }

    public function responder(){
        $opcionSeleccionada = $_POST["opcion"];
        $idPregunta = $_POST["id_pregunta"];
        $idUsuario = $_SESSION["usuario"]["id_usuario"];

        $datos = $this->model->verificarRespuesta($idPregunta, $idUsuario, $opcionSeleccionada);
        $gano = $datos['correcta'];
        $puntajePartida = $datos['puntajePartida'];
        $respuestaCorrecta = $this->model->getRespuestaCorrecta($idPregunta);
        
        // Obtener la pregunta completa para mapear la letra a su texto
        $pregunta = $this->model->getPreguntaById($idPregunta);
        $textoRespuestaCorrecta = null;
        if ($pregunta) {
            switch ($respuestaCorrecta) {
                case 'A': $textoRespuestaCorrecta = $pregunta['opcion_a']; break;
                case 'B': $textoRespuestaCorrecta = $pregunta['opcion_b']; break;
                case 'C': $textoRespuestaCorrecta = $pregunta['opcion_c']; break;
                case 'D': $textoRespuestaCorrecta = $pregunta['opcion_d']; break;
            }
        }

        $this->renderer->render("respuesta", [
            "isRespuesta" => true,
            "opcion" => $opcionSeleccionada,
            "respuestaCorrecta" => $respuestaCorrecta,
            "respuestaCorrectaTexto" => $textoRespuestaCorrecta,
            "gano" => $gano,
            "puntaje" => $puntajePartida,
            "id_pregunta"=> $idPregunta
        ]);
    }

    public function reportarPregunta(){
        $idPregunta = $_POST["id_pregunta"];
        $motivo = $_POST["motivo"];
        $comentario = $_POST["comentario"];
        $idUsuario = $_SESSION["usuario"]["id_usuario"];
        // verificar que lo que se pasa por comentario no sea un script
        $comentario = htmlspecialchars($comentario, ENT_QUOTES, 'UTF-8');
        
        $this->model->reportarPregunta($idPregunta, $idUsuario, $motivo, $comentario);

    }
}