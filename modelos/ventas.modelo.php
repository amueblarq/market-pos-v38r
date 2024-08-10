<?php

require_once "conexion.php";

class VentasModelo
{

    public $resultado;



    static public function mdlObtenerMoneda()
    {
        $stmt = Conexion::conectar()->prepare("select id,concat(id, ' - ', descripcion) as descripcion  from moneda where estado = 1;");
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
