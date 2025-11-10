<?php

class RankingController {
    private $model;
    private $renderer;

    public function __construct($model, $renderer) {
        $this->model = $model;
        $this->renderer = $renderer;
    }

    public function base() {
        $this->index();
    }

    public function index() {
        if (!isset($_SESSION["usuario"])) {
            header("Location: /user/loginForm");
            exit();
        }

        $topJugadores = $this->model->getTopJugadores(10);
        
        $idUsuarioActual = $_SESSION["usuario"]["id_usuario"];
        $posicionUsuario = $this->model->getPosicionUsuario($idUsuarioActual);
        
        $jugadoresProcesados = [];
        foreach ($topJugadores as $index => $jugador) {
            $posicion = $index + 1;
            $jugador['posicion'] = $posicion;
            $esTopTres = $posicion <= 3;
            $jugador['esPrimero'] = $posicion === 1;
            $jugador['esSegundo'] = $posicion === 2;
            $jugador['esTercero'] = $posicion === 3;
            $jugador['esTopTres'] = $esTopTres;
            
            $jugadoresProcesados[] = $jugador;
        }
        
        $data = [
            "isRanking" => true,
            "jugadores" => $jugadoresProcesados,
            "posicionUsuario" => $posicionUsuario,
            "idUsuarioActual" => $idUsuarioActual
        ];

        $this->renderer->render("ranking", $data);
    }
}

