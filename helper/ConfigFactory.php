<?php
include_once("helper/MyConexion.php");
include_once("helper/IncludeFileRenderer.php");
include_once("helper/NewRouter.php");
include_once("helper/EmailHelper.php");
include_once("controller/UserController.php");
include_once("model/GameModel.php");
include_once("controller/GameController.php");
include_once("model/UserModel.php");
include_once("controller/InicioController.php");
include_once("model/InicioModel.php");
include_once('vendor/mustache/src/Mustache/Autoloader.php');
include_once ("helper/MustacheRenderer.php");
include_once ("vendor/autoload.php");
include_once("controller/RankingController.php");
include_once("model/RankingModel.php");
include_once("controller/PerfilController.php");
include_once("model/PerfilModel.php");
include_once("controller/EditorController.php"); 
include_once("model/EditorModel.php"); 
include_once("controller/AdminController.php"); 
include_once("model/AdminModel.php"); 

class ConfigFactory
{
    private $config;
    private $objetos;

    private $conexion;
    private $renderer;

    private $emailHelper;

    public function __construct()
    {
        $this->config = parse_ini_file("config/config.ini");

        $this->conexion= new MyConexion(
            $this->config["server"],
            $this->config["user"],
            $this->config["pass"],
            $this->config["database"]
        );

        $this->renderer = new MustacheRenderer("vista");

        $this->objetos["router"] = new NewRouter($this, "UserController", "base");

        $this->objetos["UserController"] = new UserController(new UserModel($this->conexion), $this->renderer);

        $this->objetos["InicioController"] = new InicioController(new InicioModel($this->conexion), new RankingModel($this->conexion), $this->renderer);

        $this->objetos["GameController"] = new GameController(new GameModel($this->conexion), $this->renderer);        

        $this->objetos["RankingController"] = new RankingController(new RankingModel($this->conexion), $this->renderer);
        
        $this->objetos["PerfilController"] = new PerfilController(new PerfilModel($this->conexion), new RankingModel($this->conexion), $this->renderer);

        $this->emailHelper = new EmailHelper();
        $this->objetos["EditorController"] = new EditorController(new EditorModel($this->conexion), $this->renderer);

        $this->objetos["AdminController"] = new AdminController(new AdminModel($this->conexion), $this->renderer);        

    }

    public function get($objectName)
    {
        if (!isset($this->objetos[$objectName])) {
             header("location: /");
             exit;
        }
        return $this->objetos[$objectName];
    }
}