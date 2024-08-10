<?php
session_start();
include_once('modelos/conexion.php');

$msg = '';

function generate_otp($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#$&?@'; // lista de caracteres permitidos
    $otp = '';
    $max = strlen($characters) - 1;

    for ($i = 0; $i < $length; $i++) {
        $otp .= $characters[random_int(0, $max)]; // Utilizamos random_int para mayor seguridad
    }

    return $otp;
}

// Si el formulario de inicio de sesión se envió y no hay un código OTP generado
if(isset($_POST['login']) && empty($_SESSION['otp'])) {
    $email = $_POST['email'];
    
    try {
        $conexion = Conexion::conectar(); // Usar el método estático conectar de la clase Conexion

        // Preparar la consulta usando parámetros para evitar la inyección SQL
        $stmt = $conexion->prepare("SELECT id_usuario, nombre_usuario FROM usuarios WHERE correo=:email");
        $stmt->bindParam(':email', $email);
        
        if ($stmt->execute()) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data && isset($data['nombre_usuario']) && isset($data['id_usuario'])) {
                $_SESSION['name'] = $data['nombre_usuario'];
                $id_usuario = $data['id_usuario']; // Obtener el id_usuario
                $otp = generate_otp();   //Generar OTP
                $_SESSION['otp'] = $otp;

                // Insertar el OTP y el id_usuario en la tabla tbl_otp_check
                $stmt_insert = $conexion->prepare("INSERT INTO tbl_otp_check (otp, id_usuario, is_expired) VALUES (:otp, :id_usuario, 0)");
                $stmt_insert->bindParam(':otp', $otp);
                $stmt_insert->bindParam(':id_usuario', $id_usuario);
                
                if ($stmt_insert->execute()) {
                    // Correo electrónico y OTP enviados correctamente
                    include_once("SMTP/class.phpmailer.php");
                    include_once("SMTP/class.smtp.php");
                    
                    $message = '<div>
                        <p><b>Hola!</b></p>
                        <p>Estás recibiendo este correo electrónico porque hemos recibido una solicitud de un Codigo para tu cuenta.</p>
                        <br>
                        <p>Tu Codigo es: <b>'.$otp.'</b></p>
                        <br>
                        <p>Si no solicitaste un codigo, no es necesario realizar ninguna acción.</p>
                    </div>';
                    
                    $mail = new PHPMailer;
                    $mail->IsSMTP();
                    $mail->SMTPAuth = true;                 
                    $mail->SMTPSecure = "tls";      
                    $mail->Host = 'smtp.gmail.com';
                    $mail->Port = 587; 
                    $mail->Username = "jm473049@gmail.com"; // Cambia tu dirección de correo
                    $mail->Password = "nrbi wzxd ibbw jqcl"; // Cambia tu contraseña
                    $mail->FromName = "AMUEBLARQ";
                    $mail->AddAddress($email);
                    $mail->Subject = "OTP";
                    $mail->isHTML( TRUE );
                    $mail->Body =$message;
                    
                    if($mail->send()) {
                        // Mostrar el formulario para ingresar el OTP
                        $show_form = true;
                    } else {
                        $msg = "Error al enviar el correo electrónico";
                    }
                } else {
                    $msg = "Error al insertar el OTP en la base de datos.";
                }
            } else {
                $msg = "El usuario no tiene un nombre definido o no se encontraron datos válidos.";
            }
        } else {
            $msg = "Error al ejecutar la consulta SQL.";
        }
    } catch (PDOException $e) {
        $msg = "Error al conectar a la base de datos: " . $e->getMessage();
    }
}

// Verificar si el formulario de verificación de OTP se ha enviado
if(isset($_POST['verify_otp'])) {
    $otp_entered = $_POST['otp'];
    
    if(isset($_SESSION['otp'])) {
        $otp_generated = $_SESSION['otp'];
        
        if($otp_entered == $otp_generated) {
            // OTP es correcto, redireccionar a otpverify.php
            header('Location: otpverify.php');
            exit;
        } else {
            $msg = "El código OTP ingresado no es válido. Por favor, inténtelo de nuevo.";
            $show_form = true; // Mostrar el formulario de ingreso de OTP
        }
    } else {
        $msg = "No se ha generado un código OTP válido. Por favor, vuelva a enviar el formulario de inicio de sesión.";
        $show_form = false; // No mostrar ningún formulario
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Formulario de inicio de sesión</title>
    <link rel="stylesheet" href="vistas/assets/dist/css/index1_Style.css">
    <style>
        .error {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #ec5353;
            color: white;
            padding: 15px;
            border-radius: 5px;
            z-index: 9999;
            font-family: Calibri, "Segoe UI", sans-serif;
            display: none; /* Ocultar la alerta inicialmente */
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15), 0 6px 6px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            height: 3px;
            background-color: #b81414;
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            animation: progress 3s linear forwards;
            visibility: hidden; /* Ocultar la barra de progreso inicialmente */
        }

        @keyframes fadeInOut {
             0% {
                 opacity: 0;
                 visibility: hidden;
             }
             10% {
                 opacity: 1;
                 visibility: visible;
             }
             90% {
                 opacity: 1;
                 visibility: visible;
             }
             100% {
                 opacity: 0;
                 visibility: hidden;
             }
         }

         @keyframes progress {
             0% {
                 width: 100%;
             }
             100% {
                 width: 0%;
             }
         }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo-img">
            <img src="vistas\assets\imagenes\logo.jpg" alt="Logo">
        </div>
        <div class="logo3-img">
            <img src="vistas\assets\imagenes\logo3.png" alt="Logo3">
        </div>
        <div class="logo-text">AMUEBLARQ</div>
        <div class="logo-text2">¿OLVIDASTE TU CONTRASEÑA?</div>
        <div class="logo-text3">Si estás teniendo problemas para acceder a tu cuenta,  
            <br>podemos ayudarte. Ingresa tu dirección de correo electrónico y
            <br> te enviaremos un código para restablecer tu contraseña y recuperar el acceso
        </div>

        <div class="table-responsive">
            <h3 class="h3">FORMULARIO DE INICIO DE SESIÓN</h3><br />
            <div class="box">
                <?php if(isset($show_form) && $show_form): ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="otp">Ingrese el OTP enviado a su correo electrónico</label>
                        <input type="text" name="otp" id="otp" placeholder="Ingrese OTP" required
                            class="form-control" />
                    </div>
                    <div class="form-group">
                        <input type="submit" id="verify_otp" name="verify_otp" value="Verificar OTP"
                            class="btn btn-success" />
                    </div>
                </form>
                <?php else: ?>
                <form method="post" action="">
                    <div class="form-group">
                        <label for="email">Ingrese su correo electrónico de inicio de sesión</label>
                        <input type="text" name="email" id="email" placeholder="Ingrese su correo electrónico" required
                            class="form-control" />
                    </div>
                    <div class="form-group">
                        <input type="submit" id="login" name="login" value="ENVIAR" class="btn btn-success" />
                    </div>
                </form>
                <?php endif; ?>
                <?php if(!empty($msg)): ?>
                <div id="error" class="error"><?php echo $msg; ?>
                    <div class="progress-bar"></div> <!-- Barra de progreso -->
                </div> <!-- Alerta inicialmente oculta -->
                <script>
                    var errorDiv = document.getElementById('error');
                    var progressBar = errorDiv.querySelector('.progress-bar');
                    errorDiv.style.display = 'block'; // Mostrar la alerta
                    errorDiv.style.animation = 'fadeInOut 3s forwards'; // Iniciar animación de aparición/desaparición
                    progressBar.style.visibility = 'visible'; // Mostrar la barra de progreso
                    setTimeout(function(){
                        errorDiv.style.display = 'none'; // Ocultar la alerta después de 3 segundos
                    }, 3000);
                </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>
