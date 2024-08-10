<?php
session_start();

// Verificar si la sesión 'name' está vacía
if(empty($_SESSION['name'])) {
    header('location:login.php');
    exit();
}

// Verificar si la sesión 'otp_verified' no está definida
if(!isset($_SESSION['otp_verified'])) {
    // La sesión 'otp_verified' no está definida, lo que significa que el usuario no ha verificado la OTP
    // Redirigir al usuario a la página de inicio
    header('location:login.php');
    exit();
}

// Verificar si se envió el formulario de restablecimiento
if(isset($_POST['reset_password'])) {
    // Obtener los datos del formulario
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Verificar que las contraseñas coincidan
    if($password === $confirm_password) {
        // Encriptar la contraseña con crypt() y formato específico
        $salt = '$2a$07$azybxcags23425sdg23sdfhsd$'; // Define el salt
        $hashed_password = crypt($password, $salt);

        // Actualizar la contraseña en la base de datos
        try {
            // Establecer la conexión a la base de datos
            $conexion = new PDO("mysql:host=localhost;dbname=sistema-poss", "root", "");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Preparar la consulta para actualizar la contraseña
            $stmt = $conexion->prepare("UPDATE usuarios SET clave = :password, bloqueado = 0 WHERE nombre_usuario = :name");

            // Enlazar parámetros
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':name', $_SESSION['name']);

            // Ejecutar la consulta
            $stmt->execute();

            // Redirigir al usuario a la página de inicio de sesión
            header('location:Login.php');
            exit();
        } catch (PDOException $e) {
            $msg = "Error al restablecer la contraseña: " . $e->getMessage();
        }
    } else {
        $msg = "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="vistas/assets/dist/css/resetpass.css">
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
        <h3 class="h3">RESTABLECER CONTRASEÑA</h3><br />
        <div class="box">
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
    <form method="post">
    <div class="password-input">
    <input type="password" id="password" name="password" placeholder="Ingrese su nueva contraseña" required>
</div>
<div class="confirm-password-input">
    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirme su nueva contraseña" required>
</div>

        <div>
            <button type="submit" name="reset_password">RESTABLECER CONTRASEÑA</button>
        </div>
    </form>
        </div>
        </div>
</div>
</body>

</html>

