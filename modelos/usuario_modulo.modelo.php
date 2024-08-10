<?php

require_once "conexion.php";

class UsuarioModuloModelo{

    static public function mdlRegistrarUsuarioModulo($array_idModulos, $idUsuario, $id_modulo_inicio){

        $total_registros = 0;

        if($idUsuario == 1){
            $stmt = Conexion::conectar()->prepare("delete from usuario_modulo where id_usuario = :id_usuario and id_modulo != 13");
        }else{
            $stmt = Conexion::conectar()->prepare("delete from usuario_modulo where id_usuario = :id_usuario");
        }

        $stmt -> bindParam(":id_usuario",$idUsuario,PDO::PARAM_INT);
        $stmt -> execute();

        foreach ($array_idModulos as $value) { 

            if($idUsuario == 1 && $value == 13){
                $total_registros = $total_registros + 0;
            }else{

                if($value == $id_modulo_inicio){
                    $vista_inicio = 1;
                }else{
                    $vista_inicio = 0;
                }

                $stmt = Conexion::conectar()->prepare("INSERT INTO usuario_modulo(id_usuario,
                                                                                id_modulo,
                                                                                vista_inicio,
                                                                                estado)
                                                                    values(:id_usuario,
                                                                            :id_modulo,
                                                                            :vista_inicio,
                                                                            1)");

                $stmt -> bindParam(":id_usuario",$idUsuario,PDO::PARAM_INT);
                $stmt -> bindParam(":id_modulo",$value,PDO::PARAM_INT);
                $stmt -> bindParam(":vista_inicio",$vista_inicio,PDO::PARAM_INT);

                if($stmt->execute()){
                    $total_registros = $total_registros + 1;
                }else{
                    $total_registros = 0;
                }

            }

        }

        return $total_registros;
    }
}