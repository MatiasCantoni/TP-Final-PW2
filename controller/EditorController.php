<?php
class EditorController {
    private $renderer;
    private $editorModel;

    public function __construct($editorModel, $renderer) {
        $this->editorModel = $editorModel;
        $this->renderer = $renderer;
    }

    public function base() {
        $this->index();
    }


    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            !isset($_SESSION["usuario"]) ||
            !isset($_SESSION["usuario"]["tipo_usuario"]) ||
            $_SESSION["usuario"]["tipo_usuario"] != "editor"
        ) {
            header("Location: /user/loginForm");
            exit;
        }


        $preguntasPendientes = $this->editorModel->getPreguntasPendientes();
        $reportesPendientes = $this->editorModel->getReportesPendientes();



        $this->renderer->render("editorVista", [
            "usuario" => $_SESSION["usuario"]["usuario"] ?? "(sin nombre)",
            "preguntasPendientes" => $preguntasPendientes,
            "reportesPendientes" => $reportesPendientes,
            "showNavbar" => false,
            "isEditor" => true
        ]);
    }


    public function aprobarPregunta() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_POST["id_pregunta"], $_SESSION["usuario"]["id_usuario"])) {
            $this->editorModel->aprobarPregunta(
                $_POST["id_pregunta"],
                $_SESSION["usuario"]["id_usuario"]
            );
        }

        header("Location: /TP-Final-PW2/editor");
        exit;
    }

    public function rechazarPregunta() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_POST["id_pregunta"], $_SESSION["usuario"]["id_usuario"])) {
            $this->editorModel->rechazarPregunta(
                $_POST["id_pregunta"],
                $_SESSION["usuario"]["id_usuario"]
            );
        }

        header("Location: /TP-Final-PW2/editor");
        exit;
    }


    public function agregarPregunta() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["usuario"]["id_usuario"])) {
            header("Location: /TP-Final-PW2/user/loginForm");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->editorModel->crearPregunta($_POST, $_SESSION["usuario"]["id_usuario"]);
            header("Location: /TP-Final-PW2/editor");
            exit;
        } else {
            $this->renderer->render("nuevaPreguntaVista");
        }
    }




    public function editarPregunta() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_GET["id"])) {
            header("Location: /TP-Final-PW2/editor");
            exit;
        }

        $pregunta = $this->editorModel->obtenerPreguntaPorId($_GET["id"]);
        $this->renderer->render("editarPreguntaVista", ["pregunta" => $pregunta]);
    }


    public function guardarEdicion() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->editorModel->modificarPregunta($_POST);
        }
        header("Location: /TP-Final-PW2/editor");
        exit;
    }


    public function borrarPregunta() {
        if (isset($_POST["id_pregunta"])) {
            $this->editorModel->borrarPregunta($_POST["id_pregunta"]);
        }
        header("Location: /TP-Final-PW2/editor");
        exit;
    }
    public function desestimarReporte() {
        if (isset($_POST["id_reporte"])) {
            $this->editorModel->desestimarReporte($_POST["id_reporte"]);
        }
        header("Location: /TP-Final-PW2/editor");
        exit;
    }


    public function eliminarPreguntaReportada() {
        if (isset($_POST["id_reporte"], $_POST["id_pregunta"])) {
            $this->editorModel->eliminarPreguntaReportada($_POST["id_reporte"], $_POST["id_pregunta"]);
        }
        header("Location: /TP-Final-PW2/editor");
        exit;
    }


}
