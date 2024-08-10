<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Definir variables
$email = "jm473049@gmail.com"; // Cambia esto por la dirección de correo del destinatario
$message = "Por favor acceda al sistema para más detalles."; // Cambia esto por el cuerpo del mensaje que desees enviar

try {
    // Configuración del servidor SMTP
    $mail = new PHPMailer;
    $mail->IsSMTP();
    $mail->SMTPAuth = true;                 
    $mail->SMTPSecure = "tls";      
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587; 
    $mail->Username   = 'jm473049@gmail.com';                     //SMTP username
    $mail->Password   = 'nrbi wzxd ibbw jqcl';                               //SMTP password
    $mail->FromName = "AMUEBLARQ";
    $mail->AddAddress($email);
    $mail->Subject = "Nueva Orden";
    $mail->isHTML( TRUE );
    $mail->Body = $message;

    // Envío del correo
if ($mail->send()) {
    header("Location: pagina_anterior.php?success=1");
    exit();
} else {
    header("Location: pagina_anterior.php?error=1");
    exit();
}
} catch (Exception $e) {
    echo "El mensaje no pudo ser enviado. Error del servidor de correo: {$mail->ErrorInfo}";
}
?>
