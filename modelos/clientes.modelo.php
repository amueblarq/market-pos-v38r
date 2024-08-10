<?php
require_once "conexion.php";

class ClientesModelo
{
    static public function obtenerClientes()
    {
        $query = "SELECT * FROM clientes";
        $stmt = Conexion::conectar()->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
