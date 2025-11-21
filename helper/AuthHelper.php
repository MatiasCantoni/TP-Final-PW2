<?php

class AuthHelper {

    public static function checkRole($requiredRole) {
        if (!isset($_SESSION["usuario"])) {
            header("Location: /login");
            exit();
        }

        if ($_SESSION["usuario"]["tipo_usuario"] !== $requiredRole) {
            header("Location: /");
            exit();
        }
    }

    public static function checkAny($roles = []) {
        if (!isset($_SESSION["usuario"])) {
            header("Location: /login");
            exit();
        }

        if (!in_array($_SESSION["usuario"]["tipo_usuario"], $roles)) {
            header("Location: /");
            exit();
        }
    }
}
