<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <style>
        /* Estilos para el nuevo modal */
        .mi-modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .mi-modal-contenido {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 8px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
        }

        /* Botón para cerrar el nuevo modal */
        .cerrar {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .cerrar:hover,
        .cerrar:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
<br>
<br>
<br>
<br>
    <div class="container">
        <h2>Productos</h2>
        <!-- Botón para abrir el nuevo modal -->
        <button onclick="mostrarNuevoModal()">Abrir Notificaciones</button>
        <!-- El nuevo modal -->
        <div id="miModal" class="mi-modal">
            <!-- Contenido del nuevo modal -->
            <div class="mi-modal-contenido">
                <!-- Botón para cerrar el nuevo modal -->
                <span class="cerrar" onclick="cerrarNuevoModal()">&times;</span>
                <div id="notificaciones">
                    <!-- Aquí se mostrarán las notificaciones -->
                </div>
            </div>
        </div>
    </div>
    <script>
        // Función para mostrar el nuevo modal
        function mostrarNuevoModal() {
            document.getElementById("miModal").style.display = "block";
        }

        // Función para cerrar el nuevo modal manualmente
        function cerrarNuevoModal() {
            document.getElementById("miModal").style.display = "none";
        }

        // Función para cargar las notificaciones
        function cargarNotificaciones() {
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "consultar_notificaciones.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById("notificaciones").innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        // Cargar notificaciones cuando la página se carga
        cargarNotificaciones();

        // Consulta periódica a la base de datos para verificar nuevas notificaciones
        setInterval(cargarNotificaciones, 10000); // Consulta cada 10 segundos
    </script>
</body>
</html>
