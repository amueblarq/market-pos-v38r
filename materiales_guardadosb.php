<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario de producción</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        h2 {
            text-align: center;
            color: #006400;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            color: #006400;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
        .btn-container a {
            text-decoration: none;
            color: #fff;
            padding: 10px 20px;
            background-color: #006400;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-container a:hover {
            background-color: #228b22;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>INVENTARIO DE PRODUCCIÓN</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código de Orden</th>
                    <th>Categoría</th>
                    <th>Material</th>
                    <th>Descripción</th>
                    <th>Cant. Materiales</th>
                    <th>Fecha de Creación</th>
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

                // Consulta SQL para obtener los datos de los pedidos
                $sql_pedidos = "SELECT * FROM pedidosb";
                $resultado_pedidos = $conexion->query($sql_pedidos);

                // Mostrar los datos en la tabla
                if ($resultado_pedidos->num_rows > 0) {
                    while ($row_pedido = $resultado_pedidos->fetch_assoc()) {
                        $pedido_id = $row_pedido['id'];
                        $sql_materiales = "SELECT * FROM materialesb WHERE pedido_id = $pedido_id";
                        $resultado_materiales = $conexion->query($sql_materiales);

                        $rowspan = $resultado_materiales->num_rows > 0 ? $resultado_materiales->num_rows : 1;

                        // Variable para controlar si ya se ha impreso la fecha de creación
                        $fecha_creada = false;

                        if ($resultado_materiales->num_rows > 0) {
                            while ($row_material = $resultado_materiales->fetch_assoc()) {
                                // Imprimir fila solo si es la primera iteración y la fecha de creación no ha sido impresa
                                if (!$fecha_creada) {
                                    echo "<tr>";
                                    echo "<td rowspan='$rowspan'>" . $row_pedido['id'] . "</td>";
                                    echo "<td rowspan='$rowspan'>" . $row_pedido['codigo_orden'] . "</td>";
                                    echo "<td rowspan='$rowspan'>" . $row_pedido['categoria'] . "</td>";
                                    echo "<td>" . $row_material['nombre'] . "</td>";
                                    echo "<td rowspan='$rowspan'>" . $row_pedido['descripcion'] . "</td>";
                                    echo "<td>" . $row_material['cantidad'] . "</td>";
                                    echo "<td rowspan='$rowspan'>" . $row_pedido['fecha_creacion'] . "</td>";
                                    echo "</tr>";
                                    $fecha_creada = true;
                                } else {
                                    // Para el resto de iteraciones, solo imprimir las celdas de materiales
                                    echo "<tr>";
                                    echo "<td>" . $row_material['nombre'] . "</td>";
                                    echo "<td>" . $row_material['cantidad'] . "</td>";
                                    echo "</tr>";
                                }
                            }
                        } else {
                            // Si no hay materiales, imprimir una fila con la descripción y la fecha de creación
                            echo "<tr>";
                            echo "<td rowspan='$rowspan'>" . $row_pedido['id'] . "</td>";
                            echo "<td rowspan='$rowspan'>" . $row_pedido['codigo_orden'] . "</td>";
                            echo "<td rowspan='$rowspan'>" . $row_pedido['categoria'] . "</td>";
                            echo "<td rowspan='$rowspan'>" . $row_pedido['descripcion'] . "</td>";
                            echo "<td colspan='2'>No hay materiales disponibles</td>";
                            echo "<td rowspan='$rowspan'>" . $row_pedido['fecha_creacion'] . "</td>";
                            echo "</tr>";
                        }
                    }
                } else {
                    echo "<tr><td colspan='7'>No hay datos disponibles</td></tr>";
                }

                // Cerrar la conexión a la base de datos
                $conexion->close();
                ?>
            </tbody>
        </table>

        <div class="btn-container">
            <a href="http://localhost/market-pos-v38/#">Regresar</a>
        </div>
        <br>
        <div class="btn-container">
            <a href="http://localhost/market-pos-v38/kardexmateriales.php">Kardex de materiales</a>
        </div>
    </div>
</body>
</html>
