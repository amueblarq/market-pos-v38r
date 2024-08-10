<?php

require_once "conexion.php";

class UsuariosModelo{

    static public function mdlObtenerUsuariosAsignar(){
        $stmt = Conexion::conectar()->prepare("select p.id_usuario,
                                                        p.nombre_usuario,
                                                        p.apellido_usuario,
                                                        p.usuario,
                                                        p.clave,
                                                        p.correo,
                                                        p.id_perfil_usuario,
                                                        p.bloqueado,
                                                        p.estado,
                                                        ' ' as opciones
                                                from usuarios p
                                                order by p.id_usuario");

        $stmt -> execute();

        return $stmt->fetchAll();
    }

    static public function mdlObtenerListarUsuarios(){
        
        $stmt = Conexion::conectar()->prepare("select p.id_usuario,
                                                        p.nombre_usuario,
                                                        p.apellido_usuario,
                                                        p.usuario,
                                                        p.clave,
                                                        p.correo,
                                                        p.id_perfil_usuario,
                                                        p.bloqueado
                                                from usuarios p
                                                where estado = 1
                                                order by p.id_usuario");

        $stmt -> execute();

        return $stmt->fetchAll();
    }

    static public function mdlObtenerUsuarios($post)
    {

        $column = ["id_usuario", "nombre_usuario", "apellido_usuario", "usuario", "clave", "correo", "id_perfil_usuario", "bloqueado", "estado"];
    
        $query = "SELECT p.id_usuario,
                        p.nombre_usuario,
                        p.apellido_usuario,
                        p.usuario,
                        p.clave,
                        p.correo,
                        p.id_perfil_usuario,
                        p.bloqueado,
                        CASE WHEN p.estado = 1 THEN 'ACTIVO' ELSE 'INACTIVO' END AS estado
                    FROM usuarios p ";
    
        // Agregar filtros de búsqueda
        if (isset($post["search"]["value"])) {
            $query .= ' WHERE p.nombre_usuario like "%' . $post["search"]["value"] . '%"
                        or p.apellido_usuario like "%' . $post["search"]["value"] . '%"
                        or p.id_usuario like "%' . $post["search"]["value"] . '%"
                        or case when p.estado = 1 then "ACTIVO" else "INACTIVO" end like "%' . $post["search"]["value"] . '%"';
        }
    
        // Agregar ordenamiento
        if (isset($post["order"])) {
            $order_column_index = $post['order']['0']['column'];
            $order_direction = $post['order']['0']['dir'];
            $query .= " ORDER BY " . $column[$order_column_index] . " " . $order_direction;
        } else {
            $query .= " ORDER BY p.id_usuario ASC";
        }
    
        // Agregar paginación
        $limit_clause = '';
        if ($post["length"] != -1) {
            $limit_clause = " LIMIT :start, :length";
        }
    
        $stmt = Conexion::conectar()->prepare($query . $limit_clause);
    
        // Vincular parámetros
        if (!empty($search_value)) {
            $search_param = "%" . $search_value . "%";
            $stmt->bindParam(':search_value', $search_param, PDO::PARAM_STR);
        }
        if ($post["length"] != -1) {
            $start = intval($post["start"]);
            $length = intval($post["length"]);
            $stmt->bindParam(':start', $start, PDO::PARAM_INT);
            $stmt->bindParam(':length', $length, PDO::PARAM_INT);
        }
    
        $stmt->execute();
    
        $number_filter_row = $stmt->rowCount();
    
        $results = $stmt->fetchAll();
    
        $data = array();
    
        foreach ($results as $row) {
            $sub_array = array();
            $sub_array[] = $row['id_usuario'];
            $sub_array[] = $row['nombre_usuario'];
            $sub_array[] = $row['apellido_usuario'];
            $sub_array[] = $row['usuario'];
            $sub_array[] = $row['clave'];
            $sub_array[] = $row['correo'];
            $sub_array[] = $row['id_perfil_usuario'];
            $sub_array[] = $row['bloqueado'];
            $sub_array[] = $row['estado'];
            $data[] = $sub_array;
        }
    
        $stmt = Conexion::conectar()->prepare("SELECT 1 
                                                FROM usuarios");
        $stmt->execute();
        $count_all_data = $stmt->rowCount();
    
        $output = array(
            'draw' => $post['draw'],
            "recordsTotal" => $count_all_data,
            "recordsFiltered" => $number_filter_row,
            "data" => $data
        );
    
        return $output;
    }

    static public function mdlRegistrarUsuario($usuario)
{
    $dbh = Conexion::conectar();
    $respuesta = []; // Inicializar el arreglo de respuesta

    try {
        // Validar el formato del correo electrónico
        if (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $usuario['correo'])) {
            $respuesta['tipo_msj'] = 'error';
            $respuesta['msj'] = 'El formato del correo electrónico no es válido';
            return $respuesta;
        }

        // Verificar si el usuario ya existe
        $stmt = $dbh->prepare("SELECT COUNT(*) AS count FROM usuarios WHERE usuario = :usuario");
        $stmt->bindParam(':usuario', $usuario['usuario'], PDO::PARAM_STR);
        $stmt->execute();
        $result_usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el correo electrónico ya existe
        $stmt = $dbh->prepare("SELECT COUNT(*) AS count FROM usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $usuario['correo'], PDO::PARAM_STR);
        $stmt->execute();
        $result_correo = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el ID de perfil existe
        $stmt = $dbh->prepare("SELECT COUNT(*) AS count FROM perfiles WHERE id_perfil = :id_perfil");
        $stmt->bindParam(':id_perfil', $usuario['id_perfil_usuario'], PDO::PARAM_INT);
        $stmt->execute();
        $result_perfil = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si la contraseña cumple con los requisitos
        $hasNumbers = preg_match_all('/[0-9]/', $usuario['clave']);
        $hasSpecialChars = preg_match_all('/[!@#$%^&*(),.?":{}|<>]/', $usuario['clave']);

        if (strlen($usuario['clave']) < 8 || $hasNumbers < 4 || !$hasSpecialChars) {
            $respuesta['tipo_msj'] = 'error';
            $respuesta['msj'] = 'La contraseña debe tener al menos 8 caracteres, 4 números y 1 caracter especial';
            return $respuesta;
        }

        if ($result_usuario['count'] > 0 && $result_correo['count'] > 0) {
            // Tanto el usuario como el correo electrónico ya existen
            $respuesta['tipo_msj'] = 'error';
            $respuesta['msj'] = 'El nombre de usuario y el correo electrónico ya están en uso';
        } elseif ($result_usuario['count'] > 0) {
            // El nombre de usuario ya existe
            $respuesta['tipo_msj'] = 'error';
            $respuesta['msj'] = 'El nombre de usuario ya está en uso';
        } elseif ($result_correo['count'] > 0) {
            // El correo electrónico ya existe, por lo que no se permite el registro
            $respuesta['tipo_msj'] = 'error';
            $respuesta['msj'] = 'El correo electrónico ya está en uso';
        } elseif ($result_perfil['count'] == 0) {
            // El ID de perfil seleccionado no existe
            $respuesta['tipo_msj'] = 'error';
            $respuesta['msj'] = 'El ID de perfil seleccionado no existe';
        } else {
            // Encriptar la contraseña usando crypt
            $hashed_password = crypt($usuario['clave'], '$2a$07$azybxcags23425sdg23sdfhsd$');

            $stmt = $dbh->prepare("INSERT INTO usuarios(nombre_usuario, apellido_usuario, usuario, clave, correo, id_perfil_usuario, estado)
            VALUES(:nombre_usuario, :apellido_usuario, :usuario, :clave, :correo, :id_perfil_usuario, :estado)");

            $dbh->beginTransaction();
            $stmt->bindParam(':nombre_usuario', $usuario['nombre_usuario'], PDO::PARAM_STR);
            $stmt->bindParam(':apellido_usuario', $usuario['apellido_usuario'], PDO::PARAM_STR);
            $stmt->bindParam(':usuario', $usuario['usuario'], PDO::PARAM_STR);
            $stmt->bindParam(':clave', $hashed_password, PDO::PARAM_STR); // Utilizar la contraseña encriptada
            $stmt->bindParam(':correo', $usuario['correo'], PDO::PARAM_STR);
            $stmt->bindParam(':id_perfil_usuario', $usuario['id_perfil_usuario'], PDO::PARAM_INT);
            $stmt->bindParam(':estado', $usuario['estado'], PDO::PARAM_INT);
            $stmt->execute();

            $dbh->commit();

            $respuesta['tipo_msj'] = 'success';
            $respuesta['msj'] = 'Se registró el usuario correctamente';
        }
    } catch (Exception $e) {
        $dbh->rollBack();
        $respuesta['tipo_msj'] = 'error';
        $respuesta['msj'] = 'Error al registrar el usuario';
    }

    return $respuesta;
}




    
    static public function mdlActualizarUsuario($id_usuario, $nombre_usuario, $apellido_usuario, $usuario, $clave, $correo, $id_perfil_usuario, $bloqueado, $estado)
{

    try {
        $dbh = Conexion::conectar();

        $stmt = $dbh->prepare("UPDATE usuarios 
                                  SET nombre_usuario = ?,
                                      apellido_usuario = ?,
                                      usuario = ?,
                                      clave = ?,
                                      correo = ?,
                                      id_perfil_usuario = ?,
                                      bloqueado = ?,
                                      estado = ?
                                WHERE id_usuario = ?");

        $dbh->beginTransaction();
        $stmt->execute(array(
            $nombre_usuario,
            $apellido_usuario,
            $usuario,
            $clave,
            $correo,
            $id_perfil_usuario,
            $bloqueado,
            $estado,
            $id_usuario
        ));

        $dbh->commit();
        $respuesta["tipo_msj"] = "success";
        $respuesta["msj"] = "Se actualizó el usuario correctamente";
    } catch (Exception $e) {
        $dbh->rollBack();
        $respuesta["tipo_msj"] = "error";
        $respuesta["msj"] = "Error al actualizar el usuario: " . $e->getMessage();
    }

    return $respuesta;
}

    
    
}
