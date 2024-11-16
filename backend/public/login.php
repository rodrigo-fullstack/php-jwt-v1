<?php
require_once '../vendor/autoload.php';

use Firebase\JWT\JWT;

use app\database\Database;

header('Access-Control-Allow-Origin: *');

// Para corrigir problema de não encontrar o dotenv
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__FILE__, 2));
$dotenv->load();

// Verifica se foi post...
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = filter_input(INPUT_POST, 'email');
    $senha = filter_input(INPUT_POST, 'senha');
    // echo json_encode($email); Visualizando em formato JSON
    // echo json_encode(hash('md5', $senha));

    // Conectando com o bd jwt
    $pdo = Database::connection();

    // Preparando consulta sql
    $sql = $pdo->prepare('SELECT * FROM usuarios WHERE email = :email');

    // Executando consulta sql
    $sql->execute(['email' => $email]);

    // Retornando usuário...
    $userFound = $sql->fetch();

    // Pode encontrar pelo email, mas se não encontrar:
    if(!$userFound){
        // 401 não autorizado
        // Interrompe o programa com o erro, impedindo de gerar o token
        http_response_code(401);
    } 
    // echo json_encode($userFound);

    // password_verify somente funciona com senhas que foram criadas com password_hash; no caso, eu criei com o algoritmo BCRYPT
    if(!password_verify($senha, $userFound->senha)){
        http_response_code(404);
    }

    // Payload do JWT, armazenando informações do usuário sobre o token...
    /**
     * Também pode possuir: iss (instituição emissora do token) e aud (instituição destinatária de autenticação)
     * @var 'exp': É a data de expiração do token 
     * @var 'iat': É a data de criação do token 
     * @var 'email': Email do usuário do token
     */
    $key = $_ENV['KEY'];
    $payload = [
        'exp' => time() + 10,
        'iat' => time(),
        'email' => $email
    ];

    // Fazer o Encode com o JWT combinando a chave com o payload
    // A chave está localizada no .env do php
    // ES256 está dando problema no meu pc
    $encode = JWT::encode($payload, $_ENV['KEY'], 'HS256');

    echo json_encode($encode);


}