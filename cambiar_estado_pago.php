<?php
// Recibir el ID de la orden desde la solicitud POST
$id_orden = $_POST['id'];

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "sistema-poss");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Actualizar el estado de la orden a "Finalizada"
$sql = "UPDATE ordenes SET pago = 'Total' WHERE id = ?";
$stmt = $conexion->prepare($sql);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt->bind_param("i", $id_orden);

// Ejecutar la consulta preparada
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "El pago de la orden se ha cambiado con éxito.";
    } else {
        echo "No se encontró ninguna orden con el ID proporcionado.";
    }
} else {
    echo "Hubo un error al cambiar el pago de la orden: " . $conexion->error;
}

// Cerrar la conexión a la base de datos
$stmt->close();
$conexion->close();
?>