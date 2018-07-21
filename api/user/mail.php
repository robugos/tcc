<?php
require_once '../session.php';
require_once '../config.php';
require_once 'functions.php';
require_once '../inc/database.php';
require_once DBAPI;
 
// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['email'])) {
 
    // receiving the post params
    $email = $_POST['email'];

    $recover = recoverPassword('USER', $email);
    if ($recover) {
        $response["error"] = FALSE;
        echo json_encode($response);
    } else {
        $response["error"] = TRUE;
	$response["error_msg"] = "Email não cadastrado.";
        echo json_encode($response);
    }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Campos obrigatórios faltando.";
    echo json_encode($response);
}
?>