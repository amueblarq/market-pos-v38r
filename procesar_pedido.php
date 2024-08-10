<?php
// Procesamiento del pedido y envío de notificación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $codigo_orden = $_POST['codigo_orden'];
    $categoria = $_POST['categoria'];
    $descripcion = $_POST['descripcion'];
    $cantidad_materiales = $_POST['cantidad_materiales'];

    // Guardar el pedido en la base de datos
    $conexion = new mysqli("localhost", "root", "", "sistema-poss");
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Insertar el pedido en la tabla 'pedidos'
    $sql_pedido = "INSERT INTO pedidos (codigo_orden, categoria, descripcion, cantidad_materiales) 
                   VALUES ('$codigo_orden', '$categoria', '$descripcion', '$cantidad_materiales')";

    if ($conexion->query($sql_pedido) === TRUE) {
        // Obtener el ID del pedido recién insertado
        $pedido_id = $conexion->insert_id;

        // Insertar los materiales en la tabla 'materiales'
        for ($i = 1; $i <= $cantidad_materiales; $i++) {
            $nombre_material = $_POST['material_' . $i];
            $cantidad_material = $_POST['cantidad_' . $i];

            $sql_material = "INSERT INTO materiales (pedido_id, nombre, cantidad) 
                             VALUES ($pedido_id, '$nombre_material', '$cantidad_material')";

            if ($conexion->query($sql_material) !== TRUE) {
                echo "<script>alert('Error al insertar material: " . $conexion->error . "');</script>";
                // Puedes decidir qué hacer en caso de error aquí
                break;
            }
        }

        // Crear el mensaje de notificación
        $materiales_insertados = array();
        for ($i = 1; $i <= $cantidad_materiales; $i++) {
            $nombre_material = $_POST['material_' . $i];
            $cantidad_material = $_POST['cantidad_' . $i];
            $materiales_insertados[] = "$nombre_material ($cantidad_material)";
        }

        $mensaje = "Nuevo pedido recibido para la orden: $codigo_orden\nCategoría: $categoria\nDescripción: $descripcion\nCantidad de materiales: $cantidad_materiales\nMateriales insertados:\n" . implode("\n", $materiales_insertados);

        // Insertar la notificación en la tabla 'notificaciones'
        $sql_notificacion = "INSERT INTO notificaciones (mensaje) VALUES ('$mensaje')";
        if ($conexion->query($sql_notificacion) === TRUE) {
            echo "<script>
                if (confirm('¡Pedido procesado exitosamente y notificación enviada!')) {
                    window.location.href = 'http://localhost/market-pos-v38/#';
                }
            </script>";
        } else {
            echo "<script>alert('Error al insertar la notificación: " . $conexion->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error al procesar el pedido: " . $conexion->error . "');</script>";
    }

    $conexion->close();
}
?>
