<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../config/env.php';
require_once __DIR__ . '/../../model/dao/UserDAO.php';
require_once __DIR__ . '/../../../config/db-connection.php';

// Cargar las variables del .env
loadEnv(__DIR__ . '/../../../.env');

$client = new Google_Client();
// Configuración del cliente OAuth2
$client->setClientId(getenv('CLIENT_ID'));
$client->setClientSecret(getenv('CLIENT_SECRET'));
$client->setRedirectUri('http://localhost/practicas/app/controlador/oauth/google.php');

// Definir los scopes que se necesitan
$client->addScope('email');
$client->addScope('profile');

// Manejar la respuesta de Google después de la autenticación
if (isset($_GET['code'])) {
    session_start();
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email = $google_account_info->email;
    $name = $google_account_info->name;

    // Conexión a la base de datos
    $database = new Database();
    $db = $database->getConnection();
    $userDAO = new UserDAO($db);

    // Buscar o crear usuario local
    $user = $userDAO->findByEmail($email);
    if (!$user) {
        $user_id = $userDAO->createFromOAuth($name, $email);
    } else {
        $user_id = $user->getId();
    }

    $_SESSION['user'] = [
        "user_id" => $user_id,
        "name" => $name,
        "email" => $email,
        "username" => $name,
        "accessType" => "Google"
    ];
    header('Location: ../../../index.php');
    die();
}

?>