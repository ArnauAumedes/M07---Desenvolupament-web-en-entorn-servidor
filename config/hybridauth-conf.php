<?php
require_once __DIR__ . '/../config/env.php';
loadEnv(__DIR__ . '/../.env');
return [
    'callback' => 'http://localhost/practicas/app/controlador/oauth/hybridauth.php',
    'providers' => [
        'GitHub' => [
            'enabled' => true,
            'keys' => [
                'id' => getenv('GITHUB_CLIENT_ID'),      
                'secret' => getenv('GITHUB_CLIENT_SECRET') 
            ],
            'scope' => 'user:email'
        ]
    ]
];
?>