<?php
use PHPMailer\PHPMailer\PHPMailer;

class EmailHelper {

    public static function enviarToken($email, $nombre, $token) {
        $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'matiascantoni02@gmail.com';
            $mail->Password = 'osue xgls nsha jhbs'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('matiascantoni02@gmail.com', 'Juego de Preguntas');
            $mail->addAddress($email, $nombre);

            $mail->isHTML(true);
            $mail->Subject = 'Tu codigo de validacion';
            $mail->Body = "Hola $nombre,<br>Tu código de validación es: <b>$token</b><br>
                           Copialo y pegalo en la pantalla de validación.";

            $mail->send();
    }
}