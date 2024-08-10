<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="vistas/assets/dist/css/Login_Style.css">
</head>

<body>



    <div class="container">
        <div class="logo-img">
            <img src="vistas\assets\imagenes\logo.jpg" alt="Logo">
        </div>
        <div class="logo2-img">
            <img src="vistas\assets\imagenes\logo2.png" alt="Logo2">
        </div>
        <div class="logo-text">AMUEBLARQ</div> <!-- Texto "Amueblarq" en la esquina superior izquierda -->
        <div class="logo-text3">¡Bienvenido de vuelta al sistema de inventario de Amueblarq! 
             <br>Estamos listos para ayudarte a gestionar tus existencias de manera eficiente y confiable. 
                     <br>Gracias por confiar en nosotros para optimizar tu proceso de inventario. 
                                      <br>¡Comencemos a trabajar juntos!</div>
        <div class="logo-text2">BIENVENIDO DE NUEVO</div>
        <h2 class="h2"><b>INICIAR SESION</b></h2>    

        <form method="post" class="needs-validation-login" autocomplete="off" novalidate>
            <!-- USUARIO DEL SISTEMA -->
            <div class="input-group mb-2-custom">
                <input type="text" class="form-control" placeholder="Ingrese su usuario" id="loginUsuario" autocomplete="off" required>
                <div class="input-group-append"></div>
                <div class="invalid-feedback">Debe ingresar su usuario!</div>
            </div><!-- /.input-group USUARIO -->

            <!-- PASSWORD DEL USUARIO DEL SISTEMA -->
            <div class="input-group mb-3-custom">
                <input type="password" class="form-control" placeholder="Ingrese su password" id="loginPassword" autocomplete="off" required>
                <div class="input-group-append">
                </div>
                <div class="invalid-feedback">Debe ingresar su contraseña!</div>
            </div><!-- /.input-group PASSWORD -->
            
            <!-- Enlace "¿Olvidaste tu contraseña?" -->
            <div class="text-center mb-3">
                <a href="index1.php" class="forgot-password">¿Olvidaste tu contraseña?</a>
            </div>
            
            <div>
                <span class="eye" id="togglePassword">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </span>
            </div>
        </form>

        <div class="row mt-5"> <!-- Se agregó un margen superior adicional -->
            <div class="col-md-12 text-center">
                <a class="btn btn-success w-100 fw-bold" id="btnIniciarSesion">
                    <span class="text-button">INGRESAR</span>
                    <i class="fas fa-lock fs-5 text-white"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="vistas/assets/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="vistas/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="vistas/assets/dist/js/adminlte.min.js"></script>

    <script>
        $(document).ready(function() {

            $("#btnIniciarSesion").on('click', function() {
                fnc_login();
            });

            $('#loginPassword').keypress(function(e) {
                var key = e.which;
                if (key == 13) // the enter key code
                {
                    fnc_login();
                }
            });

            // Toggle password visibility
            $("#togglePassword").click(function() {
                var passwordField = $("#loginPassword");
                var icon = $("#toggleIcon");
                if (passwordField.attr("type") == "password") {
                    passwordField.attr("type", "text");
                    icon.removeClass("fas fa-eye").addClass("fas fa-eye-slash");
                } else {
                    passwordField.attr("type", "password");
                    icon.removeClass("fas fa-eye-slash").addClass("fas fa-eye");
                }
            });

            // Validación de usuario para evitar tres letras iguales consecutivas y caracteres especiales
            document.getElementById('loginUsuario').addEventListener('input', function() {
                var userInput = this.value.replace(/[^a-zA-Z0-9]/g, ''); // Elimina caracteres especiales
                var lastChar = '';
                var count = 1;
                
                for (var i = 0; i < userInput.length; i++) {
                    if (userInput.charAt(i) === lastChar) {
                        count++;
                        if (count === 3) {
                            // Reemplazar el último carácter por una cadena vacía
                            userInput = userInput.substring(0, i) + userInput.substring(i + 1);
                            count--;
                        }
                    } else {
                        lastChar = userInput.charAt(i);
                        count = 1;
                    }
                }
                
                // Asignar el valor modificado al campo de usuario
                this.value = userInput;
            });
        });

        function fnc_login() {

            var forms = document.getElementsByClassName('needs-validation-login');

            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function(form) {

                if (form.checkValidity() === true) {

                    var formData = new FormData();
                    formData.append('accion', 'login');
                    formData.append('usuario', $("#loginUsuario").val());
                    formData.append('password', $("#loginPassword").val());

                    response = SolicitudAjax("ajax/auth.ajax.php", "POST", formData);


                    if (response["tipo_msj"] == "success") {

                        // Swal.fire({
                        //     position: 'center',
                        //     icon: response["tipo_msj"],
                        //     title: response["msj"],
                        //     showConfirmButton: false,
                        //     timer: 2000
                        // })

                        mensajeToast(response["tipo_msj"], response["msj"]);

                        setInterval(() => {
                            window.location = "http://localhost/market-pos-v38/";
                        }, 1200);
                     

                    } else {
                        mensajeToast(response["tipo_msj"], response["msj"]);
                    }

                } else {
                    mensajeToast('error', 'Ingrese el usuario y contraseña');
                }

            });

        }
    </script>
</body>

</html>
