<?php
require_once '../session.php';
require_once '../config.php';
require_once 'functions.php';
require_once '../inc/database.php';
require_once DBAPI;

// json response array

$response = array("success"=>1, "interesses"=>array());
$interesses = getInterests();
$uid = $_GET["uid"];
$userinteresses = getUserInterests('USER', $uid);

if ($interesses != false) {
    $lista = array();
    $i=0;
    foreach($interesses as $interesse){    
        $lista[$i]["id"] = $interesse["ID"];
        $lista[$i]["nome"] = $interesse["NAME"];
        $lista[$i]["categoria"] = "";//$interesse["category"];
        
        $i=$i+1;
    }
    $response["interesses"] = $lista;
    //print_r($response);
    $response["userinteresses"] = $userinteresses["INTERESTS"];
    $response["success"] = 1;
    echo json_encode($response);
} else {
    $response["success"] = 0;
    $response["message"] = "Nenhum interesse encontrado.";
    echo json_encode($response);
}
?>