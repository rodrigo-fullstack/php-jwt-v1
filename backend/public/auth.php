<?php

require_once '../vendor/autoload.php';

// Não encontra bibliotecas se o autoload não estiver funcionando...
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Dotenv\Dotenv;

header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Headers: Authorization, Content-Type, x-xsrf-token, x-csrftoken, Cache-Control, X-Requested-With');

// Verifica se a classe Dotenv existe para ser instanciada
if(class_exists('Dotenv\\Dotenv')){
    // echo 'Classe Dotenv existe...';
    // Outra forma de corrigir o problema é voltando um diretório com barra invertida
    $dotenv = Dotenv::createImmutable('..\\');
    $dotenv->load();
    // echo 'Chave = ' . $_ENV['KEY'];
}

// Captura o headers determinado em login.html
$headers = apache_request_headers();

// Acessa o atributo authorization que possui authSession como variável
$authorization = $headers['Authorization'];
// echo json_encode($authorization);

$token = str_replace('Bearer ', '', $authorization);
// echo PHP_EOL . json_encode($token);

try{

    //Decodificando o token
    $decode = JWT::decode($token, 
    new Key($_ENV['KEY'], 'HS256'));

    echo json_encode($decode);
}catch(Throwable $e){
    if($e->getMessage() === 'Expired token'){
        http_response_code(401);
        die('EXPIRED');

    }

}

