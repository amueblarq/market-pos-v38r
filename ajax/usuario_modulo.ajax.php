<?php

require_once "../modelos/usuario_modulo.modelo.php";

if (isset($_POST["accion"])) {

    switch ($_POST["accion"]) {

        case 'registrar_usuario_modulo':

            $response = UsuarioModuloModelo::mdlRegistrarUsuarioModulo($_POST["id_modulosSeleccionados"], $_POST["id_Usuario"], $_POST["id_modulo_inicio"]);

            echo json_encode($response, JSON_UNESCAPED_UNICODE);

            break;

    }

}
