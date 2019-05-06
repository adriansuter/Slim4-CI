<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello ' . $args['name'] . '!');
    return $response;
});

$app->get('/status-202', function (Request $request, Response $response, $args) {
    $response = $response->withStatus(202);
    $response->getBody()->write('Status 202');
    return $response;
});

$app->run();
