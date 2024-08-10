<?php
// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "sistema-poss");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}



// Consulta para obtener las órdenes finalizadas y los datos de los clientes asociados
$sql = "SELECT o.id, o.codigo, o.categoria, op.producto, o.descripcion, o.fecha_pedido, o.fecha_finalizacion, o.costo_total, o.tipo_pago, o.porcentaje_pago, o.monto_pago, o.estado,
        c.nombres_apellidos_razon_social AS nombre_cliente, c.direccion, c.telefono
        FROM ordenes_finalizadas o
        LEFT JOIN clientes c ON o.cliente_id = c.id
        LEFT JOIN orden_producto_finalizada op ON o.id = op.orden_id
        WHERE o.estado = 'Finalizada'";
        
// Ejecutar la consulta
$resultado = $conexion->query($sql);

// Verificar si hay resultados
if ($resultado->num_rows > 0) {
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Categoría</th>
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
                <th>cliente_id</th>
                <th>Estado</th>
            </tr>";

    // Iterar sobre cada orden finalizada y generar una fila en la tabla
    while ($row = $resultado->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['codigo'] . "</td>";
        echo "<td>" . $row['categoria'] . "</td>";
        echo "<td>" . $row['descripcion'] . "</td>";
        echo "<td>" . $row['nombre_cliente'] . "</td>";
        echo "<td>" . $row['direccion'] . "</td>"; // Modificado para mostrar la dirección del cliente
        echo "<td>" . $row['telefono'] . "</td>"; // Modificado para mostrar el teléfono del cliente
        echo "<td>" . $row['costo_total'] . "</td>";
        echo "<td>" . $row['tipo_pago'] . "</td>";
        echo "<td>" . $row['porcentaje_pago'] . "</td>";
        echo "<td>" . $row['monto_pago'] . "</td>";
        echo "<td>" . $row['fecha_pedido'] . "</td>";
        echo "<td>" . $row['fecha_finalizacion'] . "</td>";
        echo "<td>" . $row['cliente_id'] . "</td>";
        echo "<td>" . $row['estado'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron órdenes finalizadas.";
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
