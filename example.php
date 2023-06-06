<?php

use Gateway\UserGateway;
use Manager\UserManager;

// переменные подключения к базе вынести в конфигурационный файл, но т.к. на проекте он уже скорее всего есть,
// просто оставимдля подключения к базе в примере использвоания
$dsn = 'mysql:dbname=db;host=127.0.0.1';
$user = 'dbuser';
$password = 'dbpass';

$pdo = new PDO($dsn, $user, $password);

$gateway = new UserGateway($pdo);
$manager = new UserManager($gateway);

$names = $_GET['names'];

$manager->getUsersByNames([
   $names
]);