<?php
// Recibir datos de la orden y del cliente desde la solicitud POST
$datos_orden = json_decode(file_get_contents("php://input"), true);

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "sistema-poss");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Generar un código de 4 dígitos aleatorio
$codigo = sprintf("%04d", mt_rand(1, 9999));

// Insertar los datos del cliente en la tabla Clientes
$sql_cliente = "INSERT INTO clientes (rtn, nombres_apellidos_razon_social, direccion, telefono) VALUES (?, ?, ?, ?)";
$stmt_cliente = $conexion->prepare($sql_cliente);
$stmt_cliente->bind_param("ssss", $datos_orden['cliente']['rtn'], $datos_orden['cliente']['nombre'], $datos_orden['cliente']['direccion'], $datos_orden['cliente']['telefono']);
$stmt_cliente->execute();
$stmt_cliente->close();

// Obtener el ID del cliente insertado
$id_cliente = $conexion->insert_id;

// Insertar los datos de la orden en la tabla Ordenes
$sql_orden = "INSERT INTO ordenes (codigo, cliente_id, categoria, descripcion, fecha_pedido, fecha_finalizacion, costo_total, tipo_pago, porcentaje_pago, monto_pago) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_orden = $conexion->prepare($sql_orden);
$stmt_orden->bind_param("sissssdssd", $codigo, $id_cliente, $datos_orden['categoria'], $datos_orden['descripcion'], $datos_orden['fechaPedido'], $datos_orden['fechaFinalizacion'], 
    $datos_orden['costoTotal'], $datos_orden['tipoPago'], $datos_orden['porcentajePago'], $datos_orden['montoPago']);
$stmt_orden->execute();

// Obtener el ID de la orden insertada
$id_orden = $conexion->insert_id;

// Insertar los productos asociados a la orden en la tabla Orden_Producto
foreach ($datos_orden['productos'] as $producto) {
    $sql_producto = "INSERT INTO orden_producto (orden_id, producto) VALUES (?, ?)";
    $stmt_producto = $conexion->prepare($sql_producto);
    $stmt_producto->bind_param("is", $id_orden, $producto);
    $stmt_producto->execute();
    $stmt_producto->close();
}

// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';

// Definir variables
$email = "jm473049@gmail.com"; // Cambia esto por la dirección de correo del destinatario
$message = "Por favor acceda al sistema para más detalles."; // Cambia esto por el cuerpo del mensaje que desees enviar

try {
    // Configuración del servidor SMTP
    $mail = new PHPMailer;
    $mail->IsSMTP();
    $mail->SMTPAuth = true;                 
    $mail->SMTPSecure = "tls";      
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587; 
    $mail->Username   = 'jm473049@gmail.com';                     //SMTP username
    $mail->Password   = 'nrbi wzxd ibbw jqcl';                               //SMTP password
    $mail->FromName = "AMUEBLARQ";
    $mail->AddAddress($email);
    $mail->Subject = "Nueva Orden";
    $mail->isHTML( TRUE );
    $mail->Body = $message;

    // Enviar el correo
    $mail->send();
    header("Location: pagina_anterior.php?success=1");
    exit();
} catch (Exception $e) {
    // Si ocurre algún error, muestra el mensaje de error
    header("Location: pagina_anterior.php?error=1");
    exit();
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>