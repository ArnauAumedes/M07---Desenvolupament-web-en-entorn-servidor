<?php
require_once __DIR__ . '/../../model/dao/UserDAO.php';
require_once __DIR__ . '/../../../config/db-connection.php';
require_once __DIR__ . '/../../../vendor/autoload.php';
$config = require __DIR__ . '/../../../config/hybridauth-conf.php';

use Hybridauth\Hybridauth;

$hybridauth = new Hybridauth($config);

$adapter = $hybridauth->authenticate('GitHub');
$userProfile = $adapter->getUserProfile();

$name = $userProfile->displayName;
$email = $userProfile->email;

$database = new Database();
$db = $database->getConnection();

// Buscar o crear usuario local
$userDAO = new UserDAO($db);
$user = $userDAO->findByEmail($email);
if (!$user) {
    $user_id = $userDAO->createFromOAuth($name, $email);
} else {
    $user_id = $user->getId();
}

session_start();
$_SESSION['user'] = [
    'user_id' => $user_id,
    'name' => $userProfile->displayName,
    'email' => $userProfile->email,
    'username' => $userProfile->displayName,
    'accessType' => 'Github',
];

header('Location: ../../../index.php');
exit;