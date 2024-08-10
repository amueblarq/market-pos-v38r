<?php

require_once "../modelos/usuarios.modelo.php";

// class AjaxPerfiles{

//     public function ajaxObtenerPerfiles(){

//         $perfiles = PerfilControlador::ctrObtenerPerfiles();

//         echo json_encode($perfiles);
//     }

// }


// if(isset($_POST['accion']) && $_POST['accion'] == 1){

//     $perfiles = new AjaxPerfiles;    
//     $perfiles->ajaxObtenerPerfiles();

// }

if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

        case 'obtener_usuarios_asignar':

            $response = UsuariosModelo::mdlObtenerUsuariosAsignar();

            echo json_encode($response, JSON_UNESCAPED_UNICODE);

            break;

        case 'obtener_usuarios': 

            $response = UsuariosModelo::mdlObtenerUsuarios($_POST);

            echo json_encode($response, JSON_UNESCAPED_UNICODE);

            break;

        case 'listar_usuarios_select': 

                $response = UsuariosModelo::mdlObtenerListarUsuarios();
    
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
                break;

        case 'editar_usuario':

                    $response = UsuariosModelo::mdlActualizarUsuario($_POST['idUsuario'], $_POST['nombre_usuario'], $_POST['apellido_usuario'],  $_POST['id_perfil_usuario'], $_POST['id_caja'], $_POST['bloqueado'], $_POST['estado'] ) ;
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    break;      

        case 'registrar_usuario':

            //Datos del formulario
            $formulario_usuario = [];
            parse_str($_POST['datos_usuario'], $formulario_usuario);

            $response = UsuariosModelo::mdlRegistrarUsuario($formulario_usuario);
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            break;
    }
}