<?php
class Conexion {
    public static function conectar() {
        $servidor = "localhost"; // Cambia esto según tu configuración
        $usuario = "root"; // Cambia esto según tu configuración
        $contrasena = ""; // Cambia esto según tu configuración
        $nombre_db = "sistema-poss"; // Cambia esto según tu configuración

        try {
            $conexion = new PDO("mysql:host=$servidor;dbname=$nombre_db", $usuario, $contrasena);
            // Establecer el modo de error de PDO a excepción
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Establecer el conjunto de caracteres
            $conexion->exec("SET CHARACTER SET utf8");
            return $conexion;
        } catch (PDOException $e) {
            // Si hay un error al conectar, lanzar una excepción
            throw new PDOException("Error al conectar a la base de datos: " . $e->getMessage());
        }
    }
}
?>

