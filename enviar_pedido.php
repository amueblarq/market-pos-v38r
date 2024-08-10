<?php
$conexion = new mysqli("localhost", "root", "", "sistema-poss");

// Verificar si se recibieron datos válidos
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['productos']) && !empty($data['cantidades'])) {
    // Conectar a la base de datos
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Procesar los datos recibidos y actualizar el stock en la base de datos
    //$productosSeleccionados = $data['productos'];
    //$cantidadesSeleccionadas = $data['cantidades'];
    //for ($i = 0; $i < count($productosSeleccionados); $i++) {
      //  $producto = $productosSeleccionados[$i];
        // Obtener la cantidad específica para este producto
        //$cantidad = $cantidadesSeleccionadas[$i];
        // Consulta SQL para actualizar el stock del producto
        //$sql = "UPDATE productos SET stock = stock - $cantidad WHERE descripcion = '$producto'";
        // Ejecutar la consulta
        //$resultado = $conexion->query($sql);
        //if (!$resultado) {
          //  echo "Error al actualizar el stock para el producto '$producto': " . $conexion->error;
        //}
    //}
    // Cerrar la conexión a la base de datos
    $conexion->close();
    // Enviar una respuesta al cliente
    echo "Pedido recibido y procesado correctamente.";
} else {
    // Enviar un mensaje de error si no se recibieron datos válidos
    echo "Error: No se recibieron datos válidos para el pedido.";
}
?>
