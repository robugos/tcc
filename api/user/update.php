<?php
require_once '../session.php';
require_once '../config.php';
require_once 'functions.php';
require_once '../inc/database.php';
require_once DBAPI;

// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['uid']) && isset($_POST['interesses'])) {
 
    // receiving the post params
    $uid = $_POST['uid'];
    $interesses = $_POST['interesses'];
    //echo $interesses;

    $update = updateInterest('USER', $uid, $interesses);
    if ($update) {
        $response["error"] = FALSE;
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Erro desconhecido no registro.";
        echo json_encode($response);
    }
} elseif (isset($_POST['id']) && isset($_POST['evento']) && isset($_POST['nota'])){

    // receiving the post params
    $uid = $_POST['id'];
    $id = $db->getIdByUid($uid);
    $evento = $_POST['evento'];
    $nota = $_POST['nota'];

    $update = $db->updateRating($id, $evento, $nota);
    if ($update) {
        $response["error"] = FALSE;
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Erro desconhecido no registro.";
        echo json_encode($response);
    }

} elseif (isset($_POST['uid']) && isset($_POST['nome']) && isset($_POST['sobrenome'])){

    // receiving the post params
    $uid = $_POST['uid'];
    //$id = $db->getIdByUid($uid);
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];

    $update = updateName('USER', $uid, $nome, $sobrenome);
    if ($update) {
        $response["error"] = FALSE;
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Erro desconhecido no registro.";
        echo json_encode($response);
    }

} elseif (isset($_POST['uid']) && isset($_POST['ativo'])){

    // receiving the post params
    $uid = $_POST['uid'];
    //$id = $db->getIdByUid($uid);
    $ativo = $_POST['ativo'];

    $update = updateActive('USER', $uid, $ativo);
    if ($update) {
        $response["error"] = FALSE;
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Erro desconhecido no registro.";
        echo json_encode($response);
    }

} elseif (isset($_POST['uid']) && isset($_POST['senha']) && isset($_POST['novasenha'])){

    // receiving the post params
    $uid = $_POST['uid'];
    //$id = $db->getIdByUid($uid);
    $senha = $_POST['senha'];
    $novasenha = $_POST['novasenha'];

    $update = changePassword('USER', $uid, $senha, $novasenha);
    if ($update) {
        $response["error"] = FALSE;
        echo json_encode($response);
    } else if ($update == "incorreta"){
        $response["error"] = TRUE;
        $response["error_msg"] = "Senha atual incorreta.";
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
        $response["error_msg"] = "Erro desconhecido no registro.";
        echo json_encode($response);
    }

} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Campos obrigatorios faltando.";
    echo json_encode($response);
}
?>