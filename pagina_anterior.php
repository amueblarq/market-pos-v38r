<?php
// Verifica si hay un parámetro 'success' en la URL
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "La orden se registró exitosamente y el correo se envió correctamente.";
} elseif (isset($_GET['error']) && $_GET['error'] == 1) {
    echo "Ocurrió un error al registrar la orden o al enviar el correo.";
} else {
    echo "Página anterior";
}
?>