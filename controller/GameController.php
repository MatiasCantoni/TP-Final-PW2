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
        AuthHelper::checkAny(["admin", "editor", "jugador"]);
        $_SESSION["pregunta_respondida"] = false;

        $idUsuario = $_SESSION["usuario"]["id_usuario"];
        $resultado = $this->model->esUsuarioAptoParaJugar($idUsuario);

        if ($resultado == false){
            $this->renderer->render("inicio", ["isInicio" => true, "mensajeError" => "Este usuario no es apto para jugar."]);
            return;
        }

        // ahora viene directo de la base
        $categorias = $this->model->getCategorias();

        $this->renderer->render("singleplayer", [
            "isSingleplayer" => true,
            "categorias" => $categorias,
            "showNavbar" => true
        ]);
    }


    public function pregunta(){
        if (isset($_SESSION["pregunta_respondida"]) && $_SESSION["pregunta_respondida"] === true) {
            header("Location: /game/singleplayer");
            exit();
        }
        $categoria = $_GET["categoria"];
        $usuario = $_SESSION["usuario"]["id_usuario"];
        $nivelUsuario = $_SESSION["usuario"]["nivel"];
        $pregunta = $this->model->getPreguntaRandom($categoria, $usuario, $nivelUsuario);

        $colorCategorias = [
            'Historia' => 'bg-historia',
            'Ciencia' => 'bg-ciencia',
            'Deportes' => 'bg-deportes',
            'Arte' => 'bg-arte',
            'Geografia' => 'bg-geografia',
            'Entretenimiento' => 'bg-entretenimiento'
        ];
        $pregunta['color_categoria'] = $colorCategorias[$categoria] ?? 'bg-default';

        $this->renderer->render("pregunta", ["pregunta" => $pregunta , "showNavBar" => true, "isPregunta" => true]);
    }

    public function responder(){
        if (!isset($_SESSION["pregunta_respondida"]) || $_SESSION["pregunta_respondida"] === true) {
            header("Location: /game/singleplayer");
            exit();
        }
        $opcionSeleccionada = $_POST["opcion"];
        $tiempo_terminado = $_POST["tiempo_terminado"] ?? '';
        $idPregunta = $_POST["id_pregunta"];
        $idUsuario = $_SESSION["usuario"]["id_usuario"];
        $_SESSION["pregunta_respondida"] = true;

        $datos = $this->model->verificarRespuesta($idPregunta, $idUsuario, $opcionSeleccionada, $tiempo_terminado);
        
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
            "id_pregunta"=> $idPregunta,
            "showNavbar" => true
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

        $categorias = $this->model->getCategorias();
        $this->renderer->render("singleplayer", ["isSingleplayer" => true, "categorias" => $categorias ,"showNavbar" => true]);
    }
}