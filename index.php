<?php

session_start();

include("helper/ConfigFactory.php");

$configFactory = new ConfigFactory();
$router = $configFactory->get("router");

// Agregamos valores por defecto para evitar warnings
$controller = $_GET["controller"] ?? "User";
$method = $_GET["method"] ?? "base";

$router->executeController($controller, $method);

