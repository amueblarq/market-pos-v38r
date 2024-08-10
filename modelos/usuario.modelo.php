<?php

require_once "conexion.php";

class UsuarioModelo
{

    static public function mdlIniciarSesion($usuario, $password)
    {
        try {
            $stmt = Conexion::conectar()->prepare("SELECT *
                                                    FROM usuarios u 
                                                    INNER JOIN perfiles p ON u.id_perfil_usuario = p.id_perfil
                                                    INNER JOIN perfil_modulo pm ON pm.id_perfil = u.id_perfil_usuario
                                                    INNER JOIN modulos m ON m.id = pm.id_modulo
                                                    WHERE u.usuario = :usuario
                                                    AND u.estado = 1
                                                    AND p.estado = 1
                                                    AND vista_inicio = 1");
    
            $stmt->bindParam(":usuario", $usuario, PDO::PARAM_STR);
    
            $stmt->execute();
    
            $respuesta = $stmt->fetchAll(PDO::FETCH_CLASS);
    
            if (count($respuesta) > 0) {
                $usuarioDB = $respuesta[0];
                if ($usuarioDB->bloqueado == 1) {
                    $respuesta["tipo_msj"] = "error";
                    $respuesta["msj"] = "La cuenta está bloqueada. Por favor, contacte al administrador.";
                } else {
                    if ($usuarioDB->clave == $password) {
                        $_SESSION["usuario"] = $usuarioDB;
                        $respuesta["tipo_msj"] = "success";
                        $respuesta["msj"] = "Usuario autenticado";
                        // Resetear intentos fallidos
                        self::resetIntentosFallidos($usuarioDB->id_usuario);
                    } else {
                        // Incrementar intentos fallidos
                        self::incrementarIntentosFallidos($usuarioDB->id_usuario);
                        // Verificar si se excedieron los intentos
                        $intentos = self::obtenerIntentosFallidos($usuarioDB->id_usuario);
                        if ($intentos >= 4) {
                            // Bloquear cuenta
                            self::bloquearCuenta($usuarioDB->id_usuario);
                            $respuesta["tipo_msj"] = "error";
                            $respuesta["msj"] = "La cuenta ha sido bloqueada debido a múltiples intentos fallidos.";
                        } else {
                            $respuesta["tipo_msj"] = "error";
                            $respuesta["msj"] = "El usuario y/o contraseña son inválidos";
                        }
                    }
                }
            } else {
                $respuesta["tipo_msj"] = "error";
                $respuesta["msj"] = "El usuario y/o contraseña son inválidos";
            }
        } catch (Exception $e) {
            $respuesta["tipo_msj"] = "error";
            $respuesta["msj"] = "Error al autenticar usuario";
        }
    
        return $respuesta;
    }

static private function incrementarIntentosFallidos($idUsuario)
{
    try {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE id_usuario = :idUsuario");
        $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
    } catch (Exception $e) {
        // Manejar el error según sea necesario
    }
}

static private function resetIntentosFallidos($idUsuario)
{
    try {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET intentos_fallidos = 0 WHERE id_usuario = :idUsuario");
        $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
    } catch (Exception $e) {
        // Manejar el error según sea necesario
    }
}

static private function obtenerIntentosFallidos($idUsuario)
{
    try {
        $stmt = Conexion::conectar()->prepare("SELECT intentos_fallidos FROM usuarios WHERE id_usuario = :idUsuario");
        $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        $intentos = $stmt->fetch(PDO::FETCH_COLUMN);
        return $intentos;
    } catch (Exception $e) {
        return 0;
    }
}

static private function bloquearCuenta($idUsuario)
{
    try {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET bloqueado = 1 WHERE id_usuario = :idUsuario");
        $stmt->bindParam(":idUsuario", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
    } catch (Exception $e) {
        // Manejar el error según sea necesario
    }
}




static public function mdlObtenerMenuUsuario($id_usuario)
    {

        $stmt = Conexion::conectar()->prepare("SELECT m.id,m.modulo,m.icon_menu,m.vista,pm.vista_inicio,
                                                    (select count(1) from modulos m1
                                                            where m1.padre_id = m.id
                                                            and exists (select 'x' from perfil_modulo pm1 
                                                                        where pm1.id_modulo = m1.id 
                                                                        and pm1.vista_inicio = 1
                                                                        AND pm1.id_perfil = u.id_perfil_usuario)) as abrir_arbol
                                                from usuarios u inner join perfiles p on u.id_perfil_usuario = p.id_perfil
                                                inner join perfil_modulo pm on pm.id_perfil = p.id_perfil
                                                inner join modulos m on m.id = pm.id_modulo
                                                where u.id_usuario = :id_usuario
                                                and (m.padre_id is null or m.padre_id = 0)
                                                order by m.orden");

        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }


    static public function mdlObtenerSubMenuUsuario($idMenu, $id_usuario)
    {

        $stmt = Conexion::conectar()->prepare("SELECT m.id,m.modulo,m.icon_menu,m.vista,pm.vista_inicio
                                                from usuarios u inner join perfiles p on u.id_perfil_usuario = p.id_perfil
                                                inner join perfil_modulo pm on pm.id_perfil = p.id_perfil
                                                inner join modulos m on m.id = pm.id_modulo
                                                where u.id_usuario = :id_usuario
                                                and m.padre_id = :idMenu
                                                order by m.orden");

        $stmt->bindParam(":idMenu", $idMenu, PDO::PARAM_STR);
        $stmt->bindParam(":id_usuario", $id_usuario, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_CLASS);
    }

}
