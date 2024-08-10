<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Header Style</title>
    <style>
        /* Estilos para el h3 en el Content Header */
        .content-header h3 {
            margin: 0; /* Elimina el margen por defecto */
            padding: 10px 0; /* Añade espaciado interno */
            font-weight: bold; /* Texto en negrita */
            font-size: 24px; /* Tamaño de fuente */
            color: #333; /* Color del texto */
        }
    </style>
</head>

<body>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- Aplicación de estilos al h3 -->
                    <h3 class="m-0 fw-bold">ORDENES FINALIDAZAS</h3>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                        <li class="breadcrumb-item">Ordenes</li>
                        <li class="breadcrumb-item active">Ordenes finalizadas</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div><!-- /.content-header -->
</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Órdenes</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px auto;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            border-right: 1px solid #ddd; /* Agregamos borde derecho */
        }

        th {
            background-color: #f2f2f2;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        /* Estilo para eliminar el borde derecho de la última columna */
        td:last-child,
        th:last-child {
            border-right: none;
        }

        /* Estilos para el botón de imprimir */
        .print-button {
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

        /* Estilos para la barra de búsqueda */
        .search-container {
            margin: 20px auto;
            text-align: center;
        }

        #searchInput {
            padding: 10px;
            width: 80%;
            max-width: 600px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <!-- Barra de búsqueda -->
    <div class="search-container">
        <input type="text" id="searchInput" onkeyup="filtrarTabla()" placeholder="Buscar por cualquier campo...">
    </div>

    <!-- Botón de imprimir arriba de la tabla -->
    <button class="print-button" onclick="imprimirTabla()">Imprimir Tabla</button>
    
    <table id="tabla_ordenes" border="2">
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Fecha de Pedido</th>
                <th>Fecha de Finalización</th>
                <th>Costo Total</th>
                <th>Tipo de Pago</th>
                <th>Porcentaje de Pago</th>
                <th>Monto de Pago</th>
                <th>Nombre del Cliente</th>
                <th>Dirección del Cliente</th>
                <th>Teléfono del Cliente</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Conectar a la base de datos
        $conexion = new mysqli("localhost", "root", "", "sistema-poss");

        // Verificar la conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Consulta para obtener las órdenes finalizadas y los datos de los clientes asociados
        $sql = "SELECT o.id, o.codigo, op.producto, o.categoria, o.descripcion, o.fecha_pedido, o.fecha_finalizacion, o.costo_total, o.tipo_pago, o.porcentaje_pago, o.monto_pago, 
                    c.nombres_apellidos_razon_social AS nombre_cliente, c.direccion, c.telefono
                FROM ordenes o
                LEFT JOIN clientes c ON o.cliente_id = c.id
                LEFT JOIN orden_producto op ON o.id = op.orden_id
                WHERE o.estado = 'finalizada'";
        $resultado = $conexion->query($sql);

        // Verificar si hay resultados
        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["codigo"] . "</td>";
                echo "<td>" . $row["producto"] . "</td>";
                echo "<td>" . $row["categoria"] . "</td>";
                echo "<td>" . $row["descripcion"] . "</td>";
                echo "<td>" . $row["fecha_pedido"] . "</td>";
                echo "<td>" . $row["fecha_finalizacion"] . "</td>";
                echo "<td>" . $row["costo_total"] . "</td>";
                echo "<td>" . $row["tipo_pago"] . "</td>";
                echo "<td>" . $row["porcentaje_pago"] . "</td>";
                echo "<td>" . $row["monto_pago"] . "</td>";
                echo "<td>" . $row["nombre_cliente"] . "</td>";
                echo "<td>" . $row["direccion"] . "</td>";
                echo "<td>" . $row["telefono"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='13'>No se encontraron órdenes finalizadas.</td></tr>";
        }

        // Cerrar la conexión a la base de datos
        $conexion->close();
        ?>
        </tbody>
    </table>

    <script>
        function imprimirTabla() {
            window.print();
        }

        function filtrarTabla() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toLowerCase();
            table = document.querySelector("table");
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
</body>

</html>



