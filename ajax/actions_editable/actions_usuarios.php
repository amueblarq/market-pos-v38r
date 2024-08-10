<?php
require_once "../../modelos/conexion.php";

if ($_POST["action"] == "edit") {
    $data = array(
        "nombre_usuario" => $_POST["nombre_usuario"],
        "apellido_usuario" => $_POST["apellido_usuario"],
        
        
        "id_perfil_usuario" => $_POST["id_perfil_usuario"],
        "bloqueado" => $_POST["bloqueado"],
        "estado" => $_POST["estado"],
        "id_usuario" => $_POST["id_usuario"]
    );

    // Verificar si se proporcionó una nueva contraseña
    if (!empty($_POST["clave"])) {
        $data["clave"] = $_POST["clave"];
        $update = "
            UPDATE usuarios
            SET nombre_usuario = :nombre_usuario,
                apellido_usuario = :apellido_usuario,
                usuario = :usuario,
                clave = :clave,
                correo = :correo,
                id_perfil_usuario = :id_perfil_usuario,
                bloqueado = :bloqueado,
                estado = :estado
            WHERE id_usuario = :id_usuario
        ";
    } else {
        // Si no se proporciona una nueva contraseña, no la actualices
        $update = "
            UPDATE usuarios
            SET nombre_usuario = :nombre_usuario,
                apellido_usuario = :apellido_usuario,
                id_perfil_usuario = :id_perfil_usuario,
                bloqueado = :bloqueado,
                estado = :estado
            WHERE id_usuario = :id_usuario
        ";
    }

    $stmt = Conexion::conectar()->prepare($update);
    $stmt->execute($data);

    echo json_encode($_POST);
}

if ($_POST["action"] == "delete") {
    $data = array(
        "id_usuario" => $_POST["id_usuario"]
    );

    $update = "
            UPDATE usuarios
            SET estado = CASE WHEN estado = 1 THEN 0 ELSE 1 END
            WHERE id_usuario = :id_usuario
        ";

    $stmt = Conexion::conectar()->prepare($update);
    $stmt->execute($data);

    echo json_encode($_POST);
}
?>
