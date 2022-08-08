<?php
require 'vendor/autoload.php';
include 'bootstrap.php';
$app = new \Slim\App();

$app->get('/messages', function($request, $response, $args) {
    return $response->write("mensagens");
});

$app->run();

