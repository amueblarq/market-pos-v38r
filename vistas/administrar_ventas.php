<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Resizable Modal</title>
<!-- Agrega Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    /* Estilos para el botón de cerrar */
    .close {
        cursor: pointer;
        color: red;
        position: absolute;
        top: 10px;
        left: 10px;
    }
    /* Estilos para el modal */
    .modal {
        display: flex;
        align-items: center; /* Centra verticalmente el contenido */
        justify-content: center; /* Centra horizontalmente el contenido */
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
        background-color: #fefefe;
        width: 90%;
        max-width: 1000px;
        height: 90%;
        max-height: 80%;
        border: 2px solid #3498db;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        position: relative;
        padding-top: 20px;
        display: flex;
        flex-direction: column; /* Alinea los elementos en columna */
        justify-content: center; /* Centra verticalmente */
        align-items: center; /* Centra horizontalmente */
    }
    .badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 5px;
        font-size: 12px;
    }
</style>
</head>
<body>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"></div> <!-- Columna vacía para ocupar la mitad del espacio -->
            <div class="col-sm-6 text-right"> <!-- Columna para el título y el botón -->
                <h2><strong>Notificaciones</strong></h2> <!-- Título en negrita -->
                <!-- Botón de notificaciones -->
                <button onclick="mostrarModal()" class="notification-button" style="border: none; background: none; position: relative;">
                    <i class="fas fa-bell" style="font-size: 28px; color: #3498db;"></i> <!-- Icono de campana -->
                    <span id="notificacionBadge" class="badge">0</span> <!-- Notificación -->
                </button>
            </div>
        </div>
        <!-- El modal -->
        <div id="myModal" class="modal" style="display: none;">
            <!-- Contenido del modal -->
            <div id="modalDraggable" class="modal-content" style="background-color: #fff;">
                <!-- Botón para cerrar el modal -->
                <span class="close" onclick="cerrarModal()" style="cursor: pointer; color: red;">
                    <i class="fas fa-times" style="font-size: 24px;"></i>
                </span>
                <div id="notificaciones" style="max-height: calc(100% - 80px); overflow-y: auto; padding: 20px;" onmousedown="startDrag(event)">
                    <!-- Aquí se mostrarán las notificaciones -->
                    <div class="notification-item" style="width: 100%; padding: 10px;">Notificación 1: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut enim ad minim veniam.</div>
                    <div class="notification-item" style="width: 100%; padding: 10px;">Notificación 2: Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</div>
                    <div class="notification-item" style="width: 100%; padding: 10px;">Notificación 3: Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident.</div>
                    <!-- Añade más notificaciones aquí si es necesario -->
                </div>
                <!-- Manijas de redimensionamiento -->
                <div id="resize-handle" onmousedown="startResize(event)" style="position: absolute; bottom: 0; right: 0; width: 20px; height: 20px; background-color: #3498db; cursor: nwse-resize;"></div>
            </div>
        </div>
    </div>
</div>
<script>
    // Cuando la página se carga, actualizamos el contador de notificaciones
    document.addEventListener("DOMContentLoaded", function() {
        actualizarContadorNotificaciones();
    });

    // Función para actualizar el contador de notificaciones
    function actualizarContadorNotificaciones() {
        // Obtenemos el número de elementos de notificación en el modal
        var numeroNotificaciones = document.querySelectorAll(".notification-item").length;

        // Actualizamos el badge con el número de notificaciones
        var badge = document.getElementById("notificacionBadge");
        badge.innerHTML = numeroNotificaciones;
        badge.style.display = "block"; // Mostramos el contador
    }
</script>
</body>
</html>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Ventas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px;
            box-sizing: border-box;
        }
        h2 {
            font-weight: bold;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table td {
            text-align: center;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #E9E9E9;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        .exito {
            background-color: #4CAF50;
        }

        .error {
            background-color: #f44336;
        }

        .notificacion {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            display: none;
            text-align: center;
        }

        .exito {
            background-color: #4CAF50;
            color: white;
        }

        .error {
            background-color: #f44336;
            color: white;
        }

        form[action="pedidos_materiales.php"],
        form[action="materiales_guardados.php"] {
            display: inline-block;
        }

        form[action="pedidos_materiales.php"] input[type="submit"],
        form[action="materiales_guardados.php"] input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 13px;
        }

        /* Estilos para la barra de búsqueda */
        #searchInput {
            width: 300px; /* Ancho deseado */
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
            margin-left: auto; /* Alinea la barra a la derecha */
            display: block;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Contenedor para la barra de búsqueda */
        .search-container {
            text-align: right; /* Alinea el contenido a la derecha */
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6">
                    <h2 class="m-0">Administrar Ordenes</h2>
                </div>
                <div class="col-md-6">
                    <ol class="breadcrumb float-md-right">
                        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                        <li class="breadcrumb-item">Ordenes</li>
                        <li class="breadcrumb-item active">Administrar Ordenes</li>
                    </ol>
                </div>
            </div>
            <!-- Barra de búsqueda -->
            <div class="row mb-2">
                <div class="col-md-12">
                    <input type="text" id="searchInput" onkeyup="filtrarTabla()" placeholder="Buscar por cualquier campo...">
                </div>
            </div>
        </div>
    </div>

     <!-- Botones de Pedido de Materiales e Imprimir Tabla -->
     <div>
        <button id="btn-imprimir" onclick="imprimirTabla()">Imprimir Tabla</button>

        <form action="materiales_guardados.php">
            <input type="submit" value="Materiales" id="btn-materiales">
        </form>
    </div>

    <script>
        function imprimirTabla() {
            window.print();
        }
    </script>

    <div class="content pb-2">
        <?php
        // Conectar a la base de datos
        $conexion = new mysqli("localhost", "root", "", "sistema-poss");

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }


        $sql = "SELECT o.id, o.codigo, o.categoria, op.producto, o.descripcion, o.fecha_pedido, o.fecha_finalizacion, o.costo_total, o.tipo_pago, o.porcentaje_pago, o.monto_pago, o.estado, o.pago, o.pedido,
        c.nombres_apellidos_razon_social AS nombre_cliente, c.direccion, c.telefono
        FROM ordenes o
        LEFT JOIN clientes c ON o.cliente_id = c.id
        LEFT JOIN orden_producto op ON o.id = op.orden_id
        WHERE o.estado = 'En proceso'";

        $resultado = $conexion->query($sql);

        // Verificar si hay resultados
        if ($resultado->num_rows > 0) {
            echo "<table>
                    <tr>
                        <th>ID</th>
                        <th>codigo</th>
                        <th>Categoría</th>
                        <th>producto</th>
                        <th>Descripción</th>
                        <th>Nombre del Cliente</th>
                        <th>Dirección del Cliente</th>
                        <th>Teléfono del Cliente</th>
                        <th>Costo Total</th>
                        <th>Tipo de Pago</th>
                        <th>Porcentaje de Pago</th>
                        <th>Monto de Pago</th>
                        <th>Fecha de Pedido</th>
                        <th>Fecha de Finalización</th>
                        <th>Estado</th>
                        <th>pedido</th>
                        <th>pago</th>
                    </tr>";

            // Iterar sobre cada orden y generar una fila en la tabla
            while ($row = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['codigo'] . "</td>";
                echo "<td>" . $row['categoria'] . "</td>";
                echo "<td>" . $row['producto'] . "</td>";
                echo "<td>" . $row['descripcion'] . "</td>";
                echo "<td>" . $row['nombre_cliente'] . "</td>";
                echo "<td>" . $row['direccion'] . "</td>";
                echo "<td>" . $row['telefono'] . "</td>";
                echo "<td>" . $row['costo_total'] . "</td>";
                echo "<td>" . $row['tipo_pago'] . "</td>";
                echo "<td>" . $row['porcentaje_pago'] . "</td>";
                echo "<td>" . $row['monto_pago'] . "</td>";
                echo "<td>" . $row['fecha_pedido'] . "</td>";
                echo "<td>" . $row['fecha_finalizacion'] . "</td>";

                /// Agregar columna para el estado y botón para cambiar el estado
echo "<td>";
if ($row['estado'] == 'Finalizada') {
    echo "<button disabled>Finalizada</button>";
} else {
    echo "<button onclick='cambiarEstado(" . $row['id'] . ", this)'>En proceso</button>";
}
echo "</td>";

// Agregar botón para cambiar el estado del pedido
echo "<td><button onclick='cambiarPedido(" . $row['id'] . ", \"" . $row['codigo'] . "\", \"" . $row['categoria'] . "\", \"" . $row['descripcion'] . "\")'>Realizar Pedido</button></td>";

// Agregar columna para el estado de pago y botón para cambiar el estado del pago
echo "<td>";
if ($row['pago'] == 'Total') {
    echo "<button disabled>Total</button>";
} else {
    echo "<button onclick='cambiarTipoPago(" . $row['id'] . ", this)'>Parcial</button>";
}
echo "</td>";

echo "</tr>";
}
echo "</table>";

} else {
    echo "No se encontraron órdenes en proceso.";
}

        // Cerrar la conexión a la base de datos
        $conexion->close();
        ?>

        <!-- Elemento para mostrar el mensaje de notificación -->
        <div id="notificacion"></div>
    </div>

    <script>
        function filtrarTabla() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toLowerCase();
            table = document.querySelector(".content table");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none"; // Oculta todas las filas inicialmente
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            tr[i].style.display = ""; // Muestra la fila si coincide
                            break;
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Script para cambiar el estado de la orden mediante AJAX -->
    <script>
        // Función para mostrar el modal
    function mostrarModal() {
        document.getElementById("myModal").style.display = "block";
    }

    // Función para cerrar el modal manualmente
    function cerrarModal() {
        document.getElementById("myModal").style.display = "none";
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
        function cambiarEstado(idOrden, boton) {
        // Realizar una solicitud AJAX para cambiar el estado de la orden
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Mostrar mensaje de éxito
                    mostrarNotificacion('El estado de la orden se ha cambiado con éxito.', 'exito');
                    // Eliminar la fila de la tabla
                    var fila = boton.parentNode.parentNode;
                    fila.parentNode.removeChild(fila);
                } else if (xhr.status === 400) {
                    // Mostrar mensaje de error en rojo
                    mostrarNotificacion(xhr.responseText, 'error');
                    console.error('Error al cambiar el estado de la orden');
                } else {
                    // Mostrar otro mensaje de error genérico
                    mostrarNotificacion('Hubo un error al cambiar el estado de la orden.', 'error');
                    console.error('Error al cambiar el estado de la orden');
                }
            }
        };
        xhr.open('POST', 'cambiar_estado.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('id=' + idOrden);
    }
        function cambiarTipoPago(idOrden, boton) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Mostrar mensaje de éxito
                    mostrarNotificacion('El tipo de pago se ha cambiado con éxito.', 'exito');
                    // Cambiar el texto del botón y deshabilitarlo
                    boton.textContent = 'Total';
                    boton.disabled = true;
                } else {
                    // Mostrar mensaje de error
                    mostrarNotificacion('Hubo un error al cambiar el tipo de pago.', 'error');
                    console.error('Error al cambiar el tipo de pago');
                }
            }
        };
        xhr.open('POST', 'cambiar_estado_pago.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('id=' + idOrden);
    }
        // Función para mostrar la notificación con un mensaje y una clase de estilo
        function mostrarNotificacion(mensaje, tipo) {
            var notificacion = document.getElementById('notificacion');
            notificacion.innerHTML = mensaje;
            notificacion.className = tipo;
            notificacion.style.display = 'block'; // Mostrar la notificación

            // Ocultar la notificación después de 3 segundos (3000 milisegundos)
            setTimeout(function() {
                notificacion.style.display = 'none';
            }, 3000);
        }

        function cambiarPedido(idOrden, codigo, categoria, descripcion) {
    // Redireccionar al formulario de pedido de materiales con los parámetros de la orden
    window.location.href = 'pedidos_materiales.php?codigo=' + codigo + '&categoria=' + categoria + '&descripcion=' + descripcion;
}

    </script>
<script>
    // Función para mostrar el modal
    function mostrarModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
        centrarModal(modal);
    }

    // Función para cerrar el modal
    function cerrarModal() {
        document.getElementById("myModal").style.display = "none";
    }

    // Función para centrar el modal en la pantalla
    function centrarModal(modal) {
        var modalContent = modal.querySelector(".modal-content");
        var windowHeight = window.innerHeight;
        var windowWidth = window.innerWidth;
        var modalHeight = modalContent.offsetHeight;
        var modalWidth = modalContent.offsetWidth;

        // Calcular la posición vertical y horizontal del modal
        var topPosition = (windowHeight - modalHeight) / 2;
        var leftPosition = (windowWidth - modalWidth) / 2;

        // Establecer la posición del modal
        modalContent.style.top = topPosition + "px";
        modalContent.style.left = leftPosition + "px";
    }
</script>>
<script>
    var offsetX, offsetY;

    function startDrag(event) {
        // Obtener la posición inicial del ratón
        offsetX = event.clientX - parseFloat(window.getComputedStyle(modalDraggable).left);
        offsetY = event.clientY - parseFloat(window.getComputedStyle(modalDraggable).top);

        // Agregar listeners para el movimiento y el final del arrastre
        document.addEventListener('mousemove', dragModal);
        document.addEventListener('mouseup', endDrag);
    }

    function dragModal(event) {
        // Calcular la nueva posición del modal
        var newX = event.clientX - offsetX;
        var newY = event.clientY - offsetY;

        // Establecer la nueva posición del modal
        modalDraggable.style.left = newX + 'px';
        modalDraggable.style.top = newY + 'px';
    }

    function endDrag(event) {
        // Remover los listeners de movimiento y final del arrastre
        document.removeEventListener('mousemove', dragModal);
        document.removeEventListener('mouseup', endDrag);
    }
</script>

</body>

</html>
