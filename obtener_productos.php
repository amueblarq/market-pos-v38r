<?php
// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "sistema-poss");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consulta SQL para obtener los datos de la tabla 'productos'
$sql = "SELECT descripcion FROM productos";
$resultado = $conexion->query($sql);

$productos = array();

// Comprobar si hay resultados
if ($resultado->num_rows > 0) {
    // Iterar sobre los resultados y agregarlos al arreglo de productos
    while ($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Cerrar la conexión a la base de datos
$conexion->close();

// Devolver los productos como JSON
echo json_encode($productos);
?>
