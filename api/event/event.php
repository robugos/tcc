<?php
require_once '../session.php';
require_once '../config.php';
require_once 'functions.php';
require_once '../inc/database.php';
require_once '../user/functions.php';
require_once DBAPI;
 
$response = array("success"=>1, "evento"=>array());
$id = (int)$_GET["id"];
$uid = $_GET["uid"];
$evento = getEventById('EVENT', $id);
if ($evento) {
	$lista = array();
	$i=0;
	$lista[$i]["id"] = $evento["ID"];
        $lista[$i]["nome"] = $evento["NAME"];
        $lista[$i]["local"] = $evento["PLACE"];
        $lista[$i]["data"] = $evento["START_TIME"];
        $lista[$i]["descricao"] = $evento["DESCRIPTION"];
        $lista[$i]["urlimg"] = $evento["COVER"];
        $lista[$i]["adimg"] = $evento["NAME"];
        
        if($uid){
                $unique = getIdByUid('USER', $uid);
                $rate = getRating('RATE', $unique, $id);
                $lista[$i]["notauser"] = $rate['RATE'];
        }else{
                $lista[$i]["notauser"] = "0";
        }
        
        $lista[$i]["nota"] = getAVGNote('RATE', $evento["ID"]);
        
        $i=$i+1;
	$response["evento"] = $lista;
	$response["success"] = 1;
	echo json_encode($response);
} else {
	$response["success"] = 0;
	$response["message"] = "Nenhum evento encontrado.";
	echo json_encode($response);
}
?>