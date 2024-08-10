<?php
// Recibir el ID de la orden desde la solicitud POST
$id_orden = $_POST['id'];

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "sistema-poss");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener el estado de pago actual de la orden
$sql_pago = "SELECT pago FROM ordenes WHERE id = ?";
$stmt_pago = $conexion->prepare($sql_pago);
if ($stmt_pago === false) {
    die("Error en la preparación de la consulta: " . $conexion->error);
}

$stmt_pago->bind_param("i", $id_orden);
$stmt_pago->execute();
$stmt_pago->store_result();

if ($stmt_pago->num_rows == 1) {
    $stmt_pago->bind_result($estado_pago);
    $stmt_pago->fetch();

    // Verificar que el estado de pago sea "Parcial" para evitar cambiar a "Finalizada"
    if ($estado_pago == 'Parcial') {
        http_response_code(400); // Código de respuesta de error
        echo "El estado de pago es 'Parcial'. No se puede cambiar el estado a 'Finalizada'.";
    } else {
        // Actualizar el estado de la orden a "Finalizada"
        $sql = "UPDATE ordenes SET estado = 'Finalizada' WHERE id = ?";
        $stmt = $conexion->prepare($sql);
        if ($stmt === false) {
            die("Error en la preparación de la consulta: " . $conexion->error);
        }

        $stmt->bind_param("i", $id_orden);

        // Ejecutar la consulta preparada
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo "El estado de la orden se ha cambiado con éxito.";
            } else {
                echo "No se encontró ninguna orden con el ID proporcionado.";
            }
        } else {
            echo "Hubo un error al cambiar el estado de la orden: " . $conexion->error;
        }

        $stmt->close();
    }
} else {
    echo "No se encontró ninguna orden con el ID proporcionado.";
}

// Cerrar la conexión a la base de datos
$stmt_pago->close();
$conexion->close();
?>
