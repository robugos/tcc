<?php
require_once '../session.php';
require_once '../config.php';
require_once 'functions.php';
require_once '../inc/database.php';
require_once DBAPI;
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['email']) && isset($_POST['senha'])) {
 
    // receiving the post params
    $email = $_POST['email'];
    $senha = $_POST['senha'];
 
    // get the usuario by email and senha
    $usuario = login('USER', $email, $senha); 
    if ($usuario) {
        // use is found
        $response["error"] = FALSE;
        $response["uid"] = $usuario['UNIQUE_ID'];
        $response["usuario"]["nome"] = utf8_encode($usuario["NAME"]);
        $response["usuario"]["sobrenome"] = utf8_encode($usuario["SURNAME"]);
        $response["usuario"]["email"] = utf8_encode($usuario["EMAIL"]);
        $response["usuario"]["criado_em"] = $usuario["CREATED"];
        $response["usuario"]["atualizado_em"] = $usuario["UPDATED"];
        $response["usuario"]["ativo"] = $usuario["ACTIVE"];
        echo json_encode($response);
    } else {
        // usuario is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Dados de login inválidos. Tente novamente.";
        echo json_encode($response);
    }
} elseif (isset($_POST['uid'])) {

    $uid = $_POST['uid'];
 
    // get the usuario by email and senha
    $usuario = find_uid('USER', $uid);
 
    if ($usuario != false) {
        // use is found
        $response["error"] = FALSE;
        $response["uid"] = $usuario["UNIQUE_ID"];
        $response["usuario"]["nome"] = utf8_encode($usuario["NAME"]);
        $response["usuario"]["sobrenome"] = utf8_encode($usuario["SURNAME"]);
        $response["usuario"]["email"] = utf8_encode($usuario["EMAIL"]);
        $response["usuario"]["criado_em"] = $usuario["CREATED"];
        $response["usuario"]["atualizado_em"] = $usuario["UPDATED"];
        $response["usuario"]["ativo"] = $usuario["ACTIVE"];
        echo json_encode($response);
    } else {
        // usuario is not found with the credentials
        $response["error"] = TRUE;
        $response["error_msg"] = "Dados de login inválidos. Tente novamente.";
        echo json_encode($response);
    }


}else {
    // required post params is missing
    $response["error"] = TRUE;
    $response["error_msg"] = "Campos obrigatórios faltando.";
    echo json_encode($response);
}
?>