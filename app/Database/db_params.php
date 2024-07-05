<?php
use Dotenv\Dotenv;

//Loading enviroment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');//main dir
$dotenv->load();


// connection variables
return [
    'driver'   => $_ENV['DB_DRIVER'],
    'host'     => $_ENV['DB_HOST'],
    'port'     => $_ENV['DB_PORT'],
    'user'     => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'dbname'   => $_ENV['DB_NAME'],
];