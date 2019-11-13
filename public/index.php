<?php /** @noinspection PhpUnusedParameterInspection */
/**
 * Slim4 CI (https://github.com/adriansuter/Slim4-CI)
 *
 * @license https://github.com/adriansuter/Slim4-CI/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

use App\Utils\SlimPsr17FactoryUtils;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

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

$app->get('/method', function (Request $request, Response $response, $args) {
    $response->getBody()->write($request->getMethod());
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

$app->get('/cookie-params', function (Request $request, Response $response, array $args): Response {
    $response->getBody()->write(
        json_encode($request->getCookieParams(), JSON_UNESCAPED_UNICODE)
    );
    return $response;
});

$app->group('/request', function (RouteCollectorProxy $group) {
    $group->get('/protocol-version', function (Request $request, Response $response, array $args): Response {
        $request = $request->withProtocolVersion('1.0');

        $response->getBody()->write(
            $request->getProtocolVersion()
        );
        return $response;
    });

    $group->get('/headers', function (Request $request, Response $response, array $args): Response {
        $response->getBody()->write(
            $request->hasHeader('slim') ?
                json_encode($request->getHeader('slim'), JSON_UNESCAPED_UNICODE) : '-'
        );

        $request = $request->withHeader('test', '1234');
        $response->getBody()->write(
            json_encode($request->getHeader('test'), JSON_UNESCAPED_UNICODE)
        );

        $request = $request->withHeader('test', '5678');
        $response->getBody()->write(
        /** @var Request $request */
            json_encode($request->getHeader('test'), JSON_UNESCAPED_UNICODE)
        );

        /** @var Request $request */
        $request = $request->withAddedHeader('test', '8765');
        $response->getBody()->write(
        /** @var Request $request */
            $request->getHeaderLine('test')
        );

        $headers = $request->getHeaders();
        $response->getBody()->write(
            json_encode($headers['test'], JSON_UNESCAPED_UNICODE)
        );

        /** @var Request $request */
        $request = $request->withoutHeader('test');
        $response->getBody()->write(
            $request->hasHeader('test') ? 'YES' : 'NO'
        );

        return $response;
    });

    $group->post('/body', function (Request $request, Response $response, array $args): Response {
        $response->getBody()->write(
            $request->getBody()->getContents()
        );

        $streamFactory = SlimPsr17FactoryUtils::getStreamFactory();
        $request = $request->withBody($streamFactory->createStream('test'));
        $response->getBody()->write(
            ',' . $request->getBody()->getContents()
        );

        return $response;
    });

    $group->get('/request-target', function (Request $request, Response $response, array $args): Response {
        $response->getBody()->write(
            $request->getRequestTarget()
        );

        $request = $request->withRequestTarget('*');
        $response->getBody()->write(
            ',' . $request->getRequestTarget()
        );

        return $response;
    });

    $group->any('/method', function (Request $request, Response $response, array $args): Response {
        $response->getBody()->write(
            $request->getMethod()
        );

        $request = $request->withMethod('PUT');
        $response->getBody()->write(
            ',' . $request->getMethod()
        );

        return $response;
    });

    $group->get('/uri', function (Request $request, Response $response, array $args): Response {
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

    $group->get('/server-params', function (Request $request, Response $response, array $args): Response {
        $serverParams = $request->getServerParams();
        $response->getBody()->write(
            isset($serverParams['REQUEST_SCHEME']) ? $serverParams['REQUEST_SCHEME'] : '-'
        );

        return $response;
    });
});


$app->get('/attributes', function (Request $request, Response $response, array $args): Response {
    $response->getBody()->write(
        $request->getAttribute('framework')
    );
    return $response;
})->add(function (Request $request, RequestHandler $handler): Response {
    $request = $request->withAttribute('framework', 'slim');
    return $handler->handle($request);
});

$app->run();
