<?php

require_once "../../modelos/conexion.php";

if ($_POST["action"] == "edit") {
    $data = array(
        "descripcion" => $_POST["descripcion"],
        "estado" => $_POST["estado"],
        "id_perfil" => $_POST["id_perfil"]
    );

    $update = "
            UPDATE perfiles
            SET descripcion = :descripcion,
                estado = :estado
            WHERE id_perfil = :id_perfil
        ";

    $stmt = Conexion::conectar()->prepare($update);
    $stmt->execute($data);

    echo json_encode($_POST);
}

if ($_POST["action"] == "delete") {

    $data = array(
        "id" => $_POST["id"]
    );

    $update = "
            UPDATE perfiles
            SET estado = CASE WHEN estado = 1 THEN 0 ELSE 1 END
            WHERE id_perfil = :id
        ";

    $stmt = Conexion::conectar()->prepare($update);
    $stmt->execute($data);

    echo json_encode($_POST);
}
?>
