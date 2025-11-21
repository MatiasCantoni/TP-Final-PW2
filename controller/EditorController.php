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

        AuthHelper::checkAny(["admin", "editor"]);


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
        AuthHelper::checkAny(["admin", "editor"]);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_POST["id_pregunta"], $_SESSION["usuario"]["id_usuario"])) {
            $this->editorModel->aprobarPregunta(
                $_POST["id_pregunta"],
                $_SESSION["usuario"]["id_usuario"]
            );
        }

        header("Location: /editor");
        exit;
    }

    public function rechazarPregunta() {
        AuthHelper::checkAny(["admin", "editor"]);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_POST["id_pregunta"], $_SESSION["usuario"]["id_usuario"])) {
            $this->editorModel->rechazarPregunta(
                $_POST["id_pregunta"],
                $_SESSION["usuario"]["id_usuario"]
            );
        }

        header("Location: /editor");
        exit;
    }


    public function agregarPregunta() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION["usuario"]["id_usuario"])) {
            header("Location: /user/loginForm");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->editorModel->crearPregunta($_POST, $_SESSION["usuario"]["id_usuario"]);
            header("Location: /editor");
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
            header("Location: /editor");
            exit;
        }

        $pregunta = $this->editorModel->obtenerPreguntaPorId($_GET["id"]);
        $this->renderer->render("editarPreguntaVista", ["pregunta" => $pregunta]);
    }


    public function guardarEdicion() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->editorModel->modificarPregunta($_POST);
        }
        header("Location: /editor");
        exit;
    }


    public function borrarPregunta() {
        if (isset($_POST["id_pregunta"])) {
            $this->editorModel->borrarPregunta($_POST["id_pregunta"]);
        }
        header("Location: /editor");
        exit;
    }
    public function desestimarReporte() {
        if (isset($_POST["id_reporte"])) {
            $this->editorModel->desestimarReporte($_POST["id_reporte"]);
        }
        header("Location: /editor");
        exit;
    }


    public function eliminarPreguntaReportada() {
        if (isset($_POST["id_reporte"], $_POST["id_pregunta"])) {
            $this->editorModel->eliminarPreguntaReportada($_POST["id_reporte"], $_POST["id_pregunta"]);
        }
        header("Location: /editor");
        exit;
    }

    public function agregarCategoria() {
        AuthHelper::checkAny(["admin", "editor"]);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar login
        if (!isset($_SESSION["usuario"])) {
            header("Location: /user/loginForm");
            exit;
        }

        // Si el formulario envió datos
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Verificar que venga el campo
            if (!empty($_POST["nueva_categoria"])) {
                $nombreCategoria = $_POST["nueva_categoria"];

                // Llamar al modelo
                $this->editorModel->agregarCategoria($nombreCategoria);
            }

            // Volver a la vista principal del editor
            header("Location: /editor");
            exit;
        }

        // Si nunca vino por POST, mostrar un error básico (opcional)
        echo "Acceso no válido.";
    }






}
