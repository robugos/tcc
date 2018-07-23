<?php
require_once '../session.php';
require_once '../config.php';
require_once 'functions.php';
require_once '../inc/database.php';
require_once DBAPI;

// json response array
$response = array("error" => FALSE);
 
if (isset($_POST['nome']) && isset($_POST['sobrenome']) && isset($_POST['email']) && isset($_POST['senha'])) {
 
    // Recebe os parâmetros do POST
    $nome = $_POST['nome'];
    $sobrenome = $_POST['sobrenome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

            // Checa se o usuário já existe
        $check = find_email('USER', $email);
        if ($check) {
            // Usuário já existe
            $response["error"] = TRUE;
            $response["error_msg"] = "E-mail já cadastrado.";
            echo json_encode($response);
        } else {
            // Cria novo usuário
            $today = date_create('now', new DateTimeZone('America/Sao_Paulo'));
            $usuario['NAME'] = $nome;
            $usuario['SURNAME'] = $sobrenome;
            $usuario['EMAIL'] = $email;
            $password = $senha;
            $usuario['PHOTO'] = "";
            $usuario['INTERESTS'] = "[]";
            $usuario['UNIQUE_ID'] = uniqid('', true);
            $hash = hashSSHA($password);
            $usuario['ENCRIPTED_PASS'] = $hash["encrypted"]; // senha encriptada
            $usuario['SALT'] = $hash["salt"]; // salt
            $usuario['UPDATED'] = $usuario['CREATED'] = $today->format("Y-m-d H:i:s");
            $usuario['ACTIVE'] = 0;

            save('USER', $usuario);
            if ($_SESSION['type'] == 'success') {
                // usuario salvo com sucesso
                $response["error"] = FALSE;
                $response["uid"] = $usuario["unique_id"];
                $response["usuario"]["nome"] = utf8_decode($usuario["NAME"]);
                $response["usuario"]["sobrenome"] =  utf8_decode($usuario["SURNAME"]);
                $response["usuario"]["email"] =  utf8_decode($usuario["EMAIL"]);
                $response["usuario"]["criado_em"] =  utf8_decode($usuario["CREATED"]);
                $response["usuario"]["atualizado_em"] =  utf8_decode($usuario["UPDATED"]);
                $response["usuario"]["ativo"] =  utf8_decode($usuario["ACTIVE"]);
                echo json_encode($response);
            } else {
                // falha ao salvar usuario
                $response["error"] = TRUE;
                $response["error_msg"] = "Erro desconhecido no registro.";
                echo json_encode($response);
            }
        }
} else {
    $response["error"] = TRUE;
    $response["error_msg"] = "Campos obrigatórios faltando.";
    echo json_encode($response);
}
?>