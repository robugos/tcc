<?php
require_once '../session.php';
require_once '../config.php';
require_once 'functions.php';
require_once '../inc/database.php';
require_once '../user/functions.php';
require_once DBAPI;

$response = array("error" => FALSE);
 
if (isset($_POST['id']) && isset($_POST['evento']) && isset($_POST['nota'])){

    // receiving the post params
    $uid = $_POST['id'];
    $id = getIdByUid('USER', $uid);
    $evento = $_POST['evento'];
    $nota = $_POST['nota'];

    $update = updateRating('RATE', $id, $evento, $nota);
    if ($update) {
        $response["error"] = FALSE;
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