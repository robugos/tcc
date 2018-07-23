<?php
require_once('../config.php');
require_once(DBAPI);
$is_dev = true;
function debug(){
    global $is_dev;
    if ($is_dev) {
        $debug_arr = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        $line      = $debug_arr[0]['line'];
        $file      = $debug_arr[0]['file'];
        header('Content-Type: text/plain');
        echo "linha: $line\n";
        echo "arquivo: $file\n\n";
        print_r(array(
            'GET' => $_GET,
            'POST' => $_POST,
            'SERVER' => $_SERVER
        ));
        exit;
    }
}
$interests = null;
$users     = null;
$user      = null;
$today     = date_create('now', new DateTimeZone('America/Sao_Paulo'));

function find_email($table = 'USER', $email = null){
    $database = open_database();
    $found    = null;
    try {
        if ($email) {
            $sql    = "SELECT * FROM " . $table . " WHERE email = '" . $email . "'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                $found = $result->fetch_assoc();
            }
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
}
function view_email($email = null){
    global $user;
    if (!empty($_POST['EMAIL']) && !empty($_POST['PASSWORD'])) {
        print_r(find_email('USER', 'contato@robugos.com'));
        if ($user) {
            $message = 'Usuário logado.';
            if (count($user) > 0) {
                $_SESSION['USER_ID'] = $user['ID'];
                header('location: /');
            } else {
                $message = "Usuário ou senha inválidos.";
            }
        }
    }
}

function hashSSHA($senha)
{
    $salt      = sha1(rand());
    $salt      = substr($salt, 0, 10);
    $encrypted = base64_encode(sha1($senha . $salt, true) . $salt);
    $hash      = array(
        "salt" => $salt,
        "encrypted" => $encrypted
    );
    return $hash;
}

function checkhashSSHA($salt, $senha){
    $hash = base64_encode(sha1($senha . $salt, true) . $salt);
    return $hash;
}

function find_uid($table = 'USER', $uid = null){
    $database = open_database();
    $found    = null;
    try {
        if ($uid) {
            $sql    = "SELECT * FROM " . $table . " WHERE unique_id = '" . $uid . "'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                $found = $result->fetch_assoc();
            }
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
}

function login($table = 'USER', $email = null, $senha = null){
    $database = open_database();
    $found    = null;
    try {
        if ($email) {
            $sql    = "SELECT * FROM " . $table . " WHERE email = '" . $email . "'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                $found     = $result->fetch_assoc();
                $salt      = $found['SALT'];
                $encripted = $found['ENCRIPTED_PASS'];
                $hash      = base64_encode(sha1($senha . $salt, true) . $salt);
                if ($encripted != $hash) {
                    $found = null;
                }
            }
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
}

function recoverPassword($table = 'USER', $email = null){
	$today     = date_create('now', new DateTimeZone('America/Sao_Paulo'));
    $database = open_database();
    $found    = null;
    try {
        if ($email) {
            $sql    = "SELECT email FROM " . $table . " WHERE email = '" . $email . "'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                $found              = $result->fetch_assoc();
                $email              = $found['EMAIL'];
                $code               = sha1(uniqid(mt_rand(), true));

                $recover['EMAIL']   = $email;
                $recover['CODE']    = $code;
                $recover['CREATED'] = $today->format("Y-m-d H:i:s");
                save('RECOVER', $recover);
                if ($_SESSION['type'] == 'success') {
                    $headers = "MIME-Version: 1.1\r\n";
                    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                    $headers .= "From: ADVinci registro@advinci.robugos.com\r\n";
                    $mensagem = "<div style='width: 100%; height: 100%; background: #F5F5F5;'>
            <div style='position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);'>
            <div style='width: 18px; height: 22px; display: block; background: url(http://advinci.robugos.com/web/images/top_left.jpg); float: left;'></div>
            <div style='width: 496px; height: 22px; display: block; background: url(http://advinci.robugos.com/web/images/top_center.jpg); float: left;'></div>
            <div style='width: 18px; height: 22px; display: block; background: url(http://advinci.robugos.com/web/images/top_right.jpg); float: left;'></div>
            <div style='width: 18px; height: 52px; display: block; background: url(http://advinci.robugos.com/web/images/middle_left.jpg); clear: left; float: left;'></div>
            <div style='width: 476px; height: 52px; display: block; background: #673ab7; float: left; padding-left: 20px; color: #FFFFFF; text-align: left; font-weight: lighter; font-size: 24px; font-family: Arial; line-height: 52px;'>Recuperar senha</div>
            <div style='width: 18px; height: 52px; display: block; background: url(http://advinci.robugos.com/web/images/middle_right.jpg); float: left;'></div>
            <div style='width: 18px; height: 100px; display: block; background: url(http://advinci.robugos.com/web/images/middle2_left.jpg); background-repeat: repeat-y; clear: left; float: left;'></div>
            <div style='width: 464px; height: 68px; display: block; background: #FFFFFF; float: left; color: #000000; text-align: center; padding: 16px; font-weight: lighter; font-size: 14px; font-family: Arial;'>
            Seu link para criar uma nova senha é <a href='http://advinci.robugos.com/web/recuperar.php?user=" . $email . "&code=" . $code . "' target='_blank'>http://advinci.robugos.com/web/recuperar.php?user=" . $email . "&code=" . $code . "</a>
            </div>
            <div style='width: 18px; height: 100px; display: block; background: url(http://advinci.robugos.com/web/images/middle2_right.jpg); background-repeat: repeat-y; float: left;'></div>
            <div style='width: 18px; height: 22px; display: block; background: url(http://advinci.robugos.com/web/images/bottom_left.jpg); clear: left; float: left;'></div>
            <div style='width: 496px; height: 22px; display: block; background: url(http://advinci.robugos.com/web/images/bottom_center.jpg); float: left;'></div>
            <div style='width: 18px; height: 22px; display: block; background: url(http://advinci.robugos.com/web/images/bottom_right.jpg); float: left;'></div>
            </div>
            </div>";
                    $envio = mail($email, "Recuperação de senha", $mensagem, $headers);
                    if ($envio) {
                        return true;
                    } else {
                        return "O email de recuperação não pôde ser enviado";
                        echo "O email de recuperação não pôde ser enviado";
                    }
                } else {
                    return "Erro ao tentar recuperar senha";
                    echo "Erro ao tentar recuperar senha";
                }
            } else {
                return "E-mail inválido";
                echo "E-mail inválido";
            }
        } else {
            return "Campos obrigatórios faltando";
            echo "Campos obrigatórios faltando";
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
        return $e->GetMessage();
        echo $e->GetMessage();
    }
    close_database($database);
    return $found;
}

function changePassword($table = 'USER', $uid, $senha, $novasenha){
    $today    = date_create('now', new DateTimeZone('America/Sao_Paulo'));
    $database = open_database();
    $found    = null;
    try {
        if ($uid) {
            $sql    = "SELECT uid, id, salt, encripted_pass FROM " . $table . " WHERE unique_id = '" . $uid . "'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                $found              = $result->fetch_assoc();
                $id                 = $found['ID'];
                $salt               = $usuario['SALT'];
                $senha_encriptada   = $usuario['ENCRIPTED_PASS'];
                $hash               = checkhashSSHA($salt, $senha);
                $usuario['UPDATED'] = $today->format("Y-m-d H:i:s");
                if ($senha_encriptada == $hash) {
                    $hash             = hashSSHA($novasenha);
                    $senha_encriptada = $hash["encrypted"]; // encrypted password
                    $salt             = $hash["salt"];
                    update('USER', $id, $usuario);
                    if ($_SESSION['type'] == 'success') {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
}

function updateName($table = 'USER', $uid = NULL, $nome = NULL, $sobrenome = NULL){
    $today    = date_create('now', new DateTimeZone('America/Sao_Paulo'));
    $database = open_database();
    $found    = null;
    try {
        if ($uid) {
            //$sql    = "SELECT uid, id FROM " . $table . " WHERE unique_id = '" . $uid . "'";
            //$result = $database->query($sql);
            //if ($result->num_rows > 0) {
	    $id = getIdByUid('USER', $uid);
            if ($id){
                //$found              = $result->fetch_assoc();
                //$id                 = $found['ID'];
                $usuario['NAME']    = $nome;
                $usuario['SURNAME'] = $sobrenome;
                $usuario['UPDATED'] = $today->format("Y-m-d H:i:s");
                update('USER', $id, $usuario);
                if ($_SESSION['type'] == 'success') {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
}

function updateActive($table = 'USER', $uid = NULL, $ativo = NULL){
    $today    = date_create('now', new DateTimeZone('America/Sao_Paulo'));
    $database = open_database();
    $found    = null;
    try {
        if ($uid) {
            //$sql    = "SELECT uid, id FROM " . $table . " WHERE unique_id = '" . $uid . "'";
            //$result = $database->query($sql);
            $id = getIdByUid('USER', $uid);
            //if ($result->num_rows > 0) {
            if ($id) {
                //$found              = $result->fetch_assoc();
                //$id                 = $found['ID'];
                $usuario['ACTIVE']    = $ativo;
                $usuario['UPDATED'] = $today->format("Y-m-d H:i:s");
                update('USER', $id, $usuario);
                if ($_SESSION['type'] == 'success') {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
}

function updateInterest($table = 'USER', $uid = NULL, $interesses = NULL){
    $today    = date_create('now', new DateTimeZone('America/Sao_Paulo'));
    $database = open_database();
    $found    = null;
    try {
        if ($uid) {
            $sql    = "SELECT * FROM " . $table . " WHERE unique_id = '" . $uid . "'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                $found                = $result->fetch_assoc();
                $id                   = $found['ID'];
                $usuario['INTERESTS'] = $interesses;
                $usuario['UPDATED']   = $today->format("Y-m-d H:i:s");
                $usuario['ACTIVE']   = 1;
                update('USER', $id, $usuario);
                if ($_SESSION['type'] == 'success') {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
}

function getInterests() {
  $database = open_database();
  $found    = null;
            $sql    = "SELECT * FROM CATEGORY";
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
    
function getUserInterests($table = 'USER', $uid = NULL) {
    $database = open_database();
    $found    = null;
    try {
        if ($uid) {
            $sql    = "SELECT INTERESTS FROM " . $table . " WHERE unique_id = '" . $uid. "'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
                $found = $result->fetch_assoc();
            }
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
    }
    
function getIdByUid($table = 'USER', $uid = NULL) {
    $database = open_database();
    $found    = null;
    try {
        if ($uid) {
            $sql    = "SELECT ID FROM " . $table . " WHERE unique_id = '" . $uid. "'";
            $result = $database->query($sql);
            if ($result->num_rows > 0) {
            	$found = $result->fetch_assoc();
                $found = $found['ID'];
            }
        }
    }
    catch (Exception $e) {
        $_SESSION['message'] = $e->GetMessage();
        $_SESSION['type']    = 'danger';
    }
    close_database($database);
    return $found;
    }
?>