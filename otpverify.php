<?php
session_start();
include_once('modelos/conexion.php');

$msg = '';

// Verificar si ya se ha verificado el OTP
if(isset($_SESSION['otp_verified']) && $_SESSION['otp_verified']) {
    header('location:reset_password.php');
    exit();
}

if(isset($_POST['otp_verify'])) {
    $otp = $_POST['otp'];
    try {
        $conexion = Conexion::conectar();

        if(isset($_SESSION['otp'])) {
            $otp_generated = $_SESSION['otp'];

            if($otp == $otp_generated) {
                // Código OTP válido
                $_SESSION['otp_verified'] = true;
                header('location:reset_password.php');
                exit();
            } else {
                // Código OTP no coincide
                $msg = "Código OTP inválido!";
            }
        } else {
            // No se ha generado un código OTP válido en la sesión
            $msg = "No se ha generado un código OTP válido. Por favor, vuelve a enviar el formulario de inicio de sesión.";
        }
    } catch (PDOException $e) {
        $msg = "Error al conectar a la base de datos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verificación de OTP</title>
    <link rel="stylesheet" href="vistas/assets/dist/css/otp_verify.css">
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
            <h3 class ="h3">Verificación de OTP</h3><br/>
            <div class="box">
                <?php if(!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']): ?>
                <form method="post">  
                    <div class="form-group">
                        <label for="otp">Ingrese el código OTP</label>
                        <input type="text" name="otp" id="otp" placeholder="Contraseña de un solo uso" required class="form-control"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" id="submit" name="otp_verify" value="VERIFICAR" class="btn btn-success" />
                    </div>
                    <p class="error"><?php if(!empty($msg)){ echo $msg; } ?></p>
                </form>
                <?php else: ?>
                <p>Ya has verificado la OTP. Por favor, cambia tu contraseña.</p>
                <form method="post" action="cambiar_contrasena.php">  
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña</label>
                        <input type="password" name="new_password" id="new_password" placeholder="Nueva Contraseña" required class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirmar Contraseña" required class="form-control"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" id="submit" name="change_password" value="Cambiar Contraseña" class="btn btn-success" />
                    </div>
                    <p class="error"><?php if(!empty($msg)){ echo $msg; } ?></p>
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