<?php

require __DIR__ . '/../lib/vendor/autoload.php';
require "Main.php";

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$configs =  [
	'settings' => [
        'displayErrorDetails' => true
        ]
    ];

$app = new Slim\App($configs);
$app->get('/', function () {

    echo "Muhammad Muhlas";
});

$app->post('/', function () {

    $response = new Main();
    $handler = $response->mainBot();

    return $handler;
});

$app->run();