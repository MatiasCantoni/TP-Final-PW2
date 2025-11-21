<?php
// Configurar zona horaria a Argentina (GMT-03:00)
date_default_timezone_set('America/Argentina/Buenos_Aires');

session_start();

include("helper/ConfigFactory.php");

$configFactory = new ConfigFactory();
$router = $configFactory->get("router");

// Agregamos valores por defecto para evitar warnings
$controller = $_GET["controller"] ?? "User";
$method = $_GET["method"] ?? "base";

$router->executeController($controller, $method);
