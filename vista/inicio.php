<?php
if (!isset($_SESSION["usuario"])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Inicio</title></head>
<body>
<h1>Bienvenido <?php echo $_SESSION["usuario"]; ?></h1>
<a href="index.php?controller=user&method=logout">Cerrar sesión</a>
</body>
</html>
