<?php
require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../../.env');
return [
  'host' => getenv('SMTP_HOST'),
  'username' => getenv('SMTP_USERNAME'),
  'password' => getenv('SMTP_PASSWORD'),
  'port' => getenv('SMTP_PORT'),
  'smtp_secure' => getenv('SMTP_SECURE'),
  'from_email' => getenv('SMTP_FROM_EMAIL'),
  'from_name' => getenv('SMTP_FROM_NAME')
];
