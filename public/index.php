<?php /** @noinspection PhpUnusedParameterInspection */
/**
 * Slim4 CI (https://github.com/adriansuter/Slim4-CI)
 *
 * @license https://github.com/adriansuter/Slim4-CI/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

$app->get('/psr-7', function (Request $request, Response $response, $args) use ($app) {
    $response->getBody()->write(
        get_class($request) . ', ' . get_class($response)
    );
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

$app->get('/request-target', function (Request $request, Response $response, $args) {
    $response->getBody()->write($request->getRequestTarget());
    return $response;
});

$app->get('/method', function (Request $request, Response $response, $args) {
    $response->getBody()->write($request->getMethod());
    return $response;
});

$app->get('/uri', function (Request $request, Response $response, $args) {
    $uri = $request->getUri();
    $response->getBody()->write(
        $uri->getScheme() . ', '
        . $uri->getAuthority() . ', '
        . $uri->getHost() . ', '
        . $uri->getPort() . ', '
        . $uri->getPath() . ', '
        . $uri->getFragment() . ', '
        . $uri->getQuery() . ', '
        . $uri->getUserInfo()
    );
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

$app->post('/upload-file', function (Request $request, Response $response, $args) {
    /** @var UploadedFileInterface[] $uploadedFiles */
    $uploadedFiles = $request->getUploadedFiles();
    foreach ($uploadedFiles as $uploadedFile) {
        $response->getBody()->write(
            $uploadedFile->getClientFilename() . ', ' . $uploadedFile->getSize()
        );
    }
    return $response;
});

$app->run();
