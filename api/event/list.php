<?php
require_once '../session.php';
require_once '../config.php';
require_once 'functions.php';
require_once '../inc/database.php';
require_once '../user/functions.php';
require_once DBAPI;
 
// json response array

$response = array("success"=>1, "eventos"=>array());

$uid = $_GET["uid"];
if ($uid == null){
    $eventos = getEvents();
}else{
    $userinteresses = getUserInterests('USER', $uid);
    $eventos = getEventsByUser('EVENT', $uid, $userinteresses);
}   

if ($eventos) {
    $lista = array();
    $i = 0;
    foreach($eventos as $evento){
        $lista[$i]["id"] = $evento["ID"];
        $lista[$i]["nome"] = $evento["NAME"];
        $lista[$i]["local"] = $evento["PLACE"];
        $lista[$i]["data"] = $evento["START_TIME"];
        $lista[$i]["descricao"] = $evento["DESCRIPTION"];
        $lista[$i]["urlimg"] = $evento["COVER"];
        $lista[$i]["adimg"] = $evento["NAME"];
        $lista[$i]["categoria"] = $evento["CATEGORY"];
        //$lista[$i]["tipo"] = $evento["TYPE"];
        $type = getTypeById('TYPE', $evento["TYPE"]);
        if ($type) {
        	$lista[$i]["tipo"] = $type["NAME"];
        }
        $lista[$i]["intersecao"] = $evento["intersecao"];
        $lista[$i]["peso"] = $evento["peso"];
        if($uid){
                $unique = getIdByUid('USER', $uid);
                $nota = getRating('RATE', $unique, $evento["ID"]);
                $lista[$i]["notauser"] = $nota['RATE'];
                //echo $lista[$i]["notauser"];
        }else{
                $lista[$i]["notauser"] = "0";
        }
        $lista[$i]["nota"] = getAVGNote('RATE', $evento["ID"]);
              
        $i=$i+1;
    }
    $response["eventos"] = $lista;
    //print_r($response);
    $response["userinteresses"] = $userinteresses['INTERESTS'];
    $response["success"] = 1;
    //print_r($lista);
    echo json_encode($response);
} else {
    $response["success"] = 0;
    $response["message"] = "Nenhum evento encontrado.";
    echo json_encode($response);
}
?>