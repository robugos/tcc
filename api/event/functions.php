<?php

require_once('../config.php');
require_once(DBAPI);

$is_dev = true;

$interests = null;
$users = null;
$user = null;

function getEvents() {

$database = open_database();
    $found    = null;
    try {
            $sql    = "SELECT * FROM EVENT";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                //$found     = $result->fetch_assoc();
                while ($row = $result->fetch_assoc()){
                	$found[] = $row;
                }
            }

    close_database($database);
    return $found;
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
    }
    
function getEventsByUser($table = 'EVENT', $uid = NULL, $userinteresses = NULL) {
	$database = open_database();
	$found    = null;
	$resultado = null;
	try {
        	if ($uid) {
			$sql    = "SELECT * FROM EVENT";
			$result = $database->query($sql);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()){
					$resultado[] = $row;
				}
		        	$interesses = explode(",", str_replace(array("[", "]", " "), "", (string)$userinteresses["INTERESTS"]));
			        foreach ($resultado as $evento){
	                		$categoria = explode(",", str_replace(array("[", "]", " "), "", $evento["CATEGORY"]));
			                if(!empty(array_intersect($categoria, $interesses))){
				                $evento["intersecao"] = array_intersect($categoria, $interesses);
				                $evento["peso"] = count(array_intersect($categoria, $interesses));
				                $found[] = $evento;
					}
				}
			}
		}
	} catch (Exception $e) {
	        $_SESSION['message'] = $e->GetMessage();
        	$_SESSION['type']    = 'danger';
	}
    close_database($database);
    return $found;
}

function getTypeById($table = 'TYPE', $id = NULL) {

$database = open_database();
    $found    = null;
    try {
            $sql    = "SELECT * FROM " . $table . " WHERE id = '". $id ."'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                $found     = $result->fetch_assoc();
            }

    close_database($database);
    return $found;
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
    }

function getEventById($table = 'EVENT', $id = null) {
	$database = open_database();
	$found    = null;
	try {
		$sql    = "SELECT * FROM ". $table ." WHERE ID ='". $id ."'";
		$result = $database->query($sql);
		if ($result->num_rows > 0) {
			$found = $result->fetch_assoc();
		}
	} catch (Exception $e) {
		$_SESSION['message'] = $e->GetMessage();
		$_SESSION['type']    = 'danger';
	}
	close_database($database);
	return $found;
}

    
function getAVGNote($table = 'RATE', $evento = null) {
	$database = open_database();
	$found    = null;
	try {
		$sql    = "SELECT AVG(RATE) AS 'RATE' FROM ". $table ." WHERE ID_EVENT ='". $evento ."'";
		$result = $database->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()){
				$row = round($row["RATE"],1);  
				$found = (string)$row;
			}
		}
	} catch (Exception $e) {
		$_SESSION['message'] = $e->GetMessage();
		$_SESSION['type']    = 'danger';
	}
	close_database($database);
	return $found;
}
    
function getRating($table = 'RATE', $id = NULL, $evento = NULL) {
	$database = open_database();
	$found = null;
	try {
		if ($id && $evento) {
			$sql = "SELECT * FROM " . $table . " WHERE id_user = '" . $id . "' AND id_event = '". $evento ."'";
			$result = $database->query($sql);
			if ($result->num_rows > 0) {
				$found = $result->fetch_assoc();
			}
		}
	}

	catch(Exception $e) {
		$_SESSION['message'] = $e->GetMessage();
		$_SESSION['type'] = 'danger';
	}

	close_database($database);
	return $found;
    }

function updateRating($table = 'RATE', $id = NULL, $evento = NULL, $nota = NULL) {
	$today     = date_create('now', new DateTimeZone('America/Sao_Paulo'));
	$database = open_database();
	$found = null;
	try {
		$historic['ID_USER'] = $id;
		$historic['ID_EVENT'] = $evento;
		$historic['RATE'] = $nota;
		$historic['CREATED'] = $historic['UPDATED'] = $today->format("Y-m-d H:i:s");
		save('HISTORIC', $historic);
		
		$rate = getRating($table, $id, $evento);
		if ($rate && $nota) {
			$rate['RATE'] = $nota;
			update($table, $rate['ID'], $rate);
			if ($_SESSION['type'] == 'success') {
				return true;
			} else {
				return false;
			}
		}elseif($nota && $evento && $id){
			$rate['RATE'] = $nota;
			$rate['ID_USER'] = $id;
			$rate['ID_EVENT'] = $evento;
			save($table, $rate);
			if ($_SESSION['type'] == 'success') {
				return true;
			} else {
				return false;
			}
		}
	}catch(Exception $e) {
		$_SESSION['message'] = $e->GetMessage();
		$_SESSION['type'] = 'danger';
	}

	close_database($database);
	return $found;
}

?>