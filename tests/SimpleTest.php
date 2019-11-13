<?php
/**
 * Slim4 CI (https://github.com/adriansuter/Slim4-CI)
 *
 * @license https://github.com/adriansuter/Slim4-CI/blob/master/LICENSE (MIT License)
 */

declare(strict_types=1);

namespace Slim\Tests\CI;

use PHPUnit\Framework\TestCase;
use Robtimus\Multipart\MultipartFormData;

class SimpleTest extends TestCase
{
    public function testGet()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello world!', $response->getBody());
    }

    public function testGetPsr7()
    {
        $expectedBodyContents = [
            'Slim' => 'Slim\Psr7\Request, Slim\Psr7\Response',
            'Nyholm' => 'Nyholm\Psr7\ServerRequest, Nyholm\Psr7\Response',
            'Guzzle' => 'GuzzleHttp\Psr7\ServerRequest, GuzzleHttp\Psr7\Response',
            'Zend' => 'Zend\Diactoros\ServerRequest, Zend\Diactoros\Response',
        ];

        $psr7 = getenv('PSR7');
        if ($psr7 === false || !isset($expectedBodyContents[$psr7])) {
            $this->markTestSkipped();
            return;
        }

        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/psr-7');

        $this->assertEquals(
            $expectedBodyContents[$psr7],
            $response->getBody()
        );
    }

    public function testGetPlaceholder()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/hello/slim');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello slim!', $response->getBody());
    }

    public function testGetQueryParams()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/query-params', ['foo' => 'bar', 'white space' => 'hello world']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foo:bar|white_space:hello world', $response->getBody());
    }

    public function testGetWithStatus()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/status-202');

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals('Status 202', $response->getBody());
        $this->assertEqualsIgnoringCase('accepted', $response->getStatusReasonPhrase());
    }

    public function testGetWithStatusAndReasonPhrase()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/status-reason-phrase');

        $this->assertEquals(299, $response->getStatusCode());
        $this->assertEqualsIgnoringCase('peace', $response->getStatusReasonPhrase());
        $this->assertEquals('Status 299 - Peace', $response->getBody());
    }

    public function testGetRequestTarget()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/request-target');

        $this->assertEquals('/request-target', $response->getBody());
    }

    public function testGetMethod()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/method');

        $this->assertEquals('GET', $response->getBody());
    }

    public function testGetUri()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/uri?foo=bar#section');

        $this->assertEquals('http, localhost, localhost, , /uri, , foo=bar, ', $response->getBody());
    }

    public function testGetRedirect()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/redirect');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Redirected', $response->getBody());
    }

    public function testPostFormData()
    {
        $client = new SimpleRequestClient();
        $response = $client->post('http://localhost/form-data', [
            'foo' => 'bar',
            'multi' => [
                '9',
                '8',
            ]
        ]);

        $this->assertEquals('{"foo":"bar","multi":["9","8"]}', $response->getBody());
    }

    public function testPostFileUpload()
    {
        $client = new SimpleRequestClient();

        $multipart = new MultipartFormData();

        $file = __DIR__ . '/assets/plain.txt';
        $multipart->addFile(
            'upload',
            'plain.txt',
            fopen($file, 'r'),
            'text/plain',
            filesize($file)
        );

        $response = $client->postMultipart('http://localhost/upload-file', $multipart);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('plain.txt, 8', $response->getBody());
    }

    public function testCookieParams()
    {
        $requestClient = new SimpleRequestClient();
        $requestClient->setCookies(['token' => 'slim', 'session' => 'foo-bar']);

        $response = $requestClient->get('http://localhost/cookie-params');
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('{"token":"slim","session":"foo-bar"}', $response->getBody());
    }

    // `\Psr\Http\Message\MessageInterface::withProtocolVersion()`
    // `\Psr\Http\Message\MessageInterface::getProtocolVersion()`
    public function testRequestProtocolVersion()
    {
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/request/protocol-version');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('1.0', $response->getBody());
    }

    // `\Psr\Http\Message\MessageInterface::getHeaders()`
    // `\Psr\Http\Message\MessageInterface::hasHeader()`
    // `\Psr\Http\Message\MessageInterface::getHeader()`
    // `\Psr\Http\Message\MessageInterface::getHeaderLine()`
    // `\Psr\Http\Message\MessageInterface::withHeader()`
    // `\Psr\Http\Message\MessageInterface::withAddedHeader()`
    // `\Psr\Http\Message\MessageInterface::withoutHeader()`
    public function testRequestHeaders()
    {
        $requestClient = new SimpleRequestClient();
        $requestClient->setHeaders([
            'slim: 9'
        ]);
        $response = $requestClient->get('http://localhost/request/headers');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('["9"]["1234"]["5678"]5678,8765["5678","8765"]NO', $response->getBody());
    }

    // `\Psr\Http\Message\MessageInterface::getBody()`
    // `\Psr\Http\Message\MessageInterface::withBody()`
    public function testRequestBody()
    {
        $requestClient = new SimpleRequestClient();
        $response = $requestClient->post('http://localhost/request/body', ['foo' => 'bar']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foo=bar,test', $response->getBody());
    }

    // `\Psr\Http\Message\RequestInterface::getRequestTarget()`
    // `\Psr\Http\Message\RequestInterface::withRequestTarget()`
    public function testRequestTarget()
    {
        $requestClient = new SimpleRequestClient();
        $response = $requestClient->get('http://localhost/request/request-target');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('/request/request-target,*', $response->getBody());
    }

    public function testAttributes()
    {
        // The request handler on the server is using the following methods (which we test here):
        // - `\Psr\Http\Message\ServerRequestInterface::withAttribute()`
        // - `\Psr\Http\Message\ServerRequestInterface::getAttribute()`
        $client = new SimpleRequestClient();
        $response = $client->get('http://localhost/attributes');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('slim', $response->getBody());
    }
}
