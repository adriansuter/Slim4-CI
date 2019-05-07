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

$app->get('/query-params', function (Request $request, Response $response, $args) {
    $params = [];
    foreach ($request->getQueryParams() as $name => $value) {
        array_push($params, $name . ':' . $value);
    }
    $response->getBody()->write(implode('|', $params));
    return $response;
});

$app->get('/status-202', function (Request $request, Response $response, $args) {
    $response = $response->withStatus(202);
    $response->getBody()->write('Status 202');
    return $response;
});

$app->get('/status-reason-phrase', function (Request $request, Response $response, $args) {
    $response = $response->withStatus(299, 'Peace');
    $response->getBody()->write('Status 299 - Peace');
    return $response;
});

$app->get('/redirect', function (Request $request, Response $response, $args) {
    return $response
        ->withHeader('Location', '/redirected')
        ->withStatus(301);
});

$app->get('/redirected', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Redirected');
    return $response;
});

$app->post('/form-data', function (Request $request, Response $response, $args) {
    $response->getBody()->write(
        json_encode($request->getParsedBody(), JSON_UNESCAPED_UNICODE)
    );
    return $response;
});

$app->run();
