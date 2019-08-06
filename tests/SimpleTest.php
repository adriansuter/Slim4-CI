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
        $client = new SimpleClient();
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

        $client = new SimpleClient();
        $response = $client->get('http://localhost/psr-7');

        $this->assertEquals(
            $expectedBodyContents[$psr7],
            $response->getBody()
        );
    }

    public function testGetPlaceholder()
    {
        $client = new SimpleClient();
        $response = $client->get('http://localhost/hello/slim');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello slim!', $response->getBody());
    }

    public function testGetQueryParams()
    {
        $client = new SimpleClient();
        $response = $client->get('http://localhost/query-params', ['foo' => 'bar', 'white space' => 'hello world']);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foo:bar|white_space:hello world', $response->getBody());
    }

    public function testGetWithStatus()
    {
        $client = new SimpleClient();
        $response = $client->get('http://localhost/status-202');

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals('Status 202', $response->getBody());
        $this->assertEqualsIgnoringCase('accepted', $response->getStatusReasonPhrase());
    }

    public function testGetWithStatusAndReasonPhrase()
    {
        $client = new SimpleClient();
        $response = $client->get('http://localhost/status-reason-phrase');

        $this->assertEquals(299, $response->getStatusCode());
        $this->assertEqualsIgnoringCase('peace', $response->getStatusReasonPhrase());
        $this->assertEquals('Status 299 - Peace', $response->getBody());
    }

    public function testGetRequestTarget()
    {
        $client = new SimpleClient();
        $response = $client->get('http://localhost/request-target');

        $this->assertEquals('/request-target', $response->getBody());
    }

    public function testGetMethod()
    {
        $client = new SimpleClient();
        $response = $client->get('http://localhost/method');

        $this->assertEquals('GET', $response->getBody());
    }

    public function testGetUri()
    {
        $client = new SimpleClient();
        $response = $client->get('http://localhost/uri?foo=bar#section');

        $this->assertEquals('http, localhost, localhost, , /uri, , foo=bar, ', $response->getBody());
    }

    public function testGetRedirect()
    {
        $client = new SimpleClient();
        $response = $client->get('http://localhost/redirect');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Redirected', $response->getBody());
    }

    public function testPostFormData()
    {
        $client = new SimpleClient();
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
        $client = new SimpleClient();

        $multipart = new MultipartFormData();
        $multipart->addFile(
            'upload',
            'plain.txt',
            file_get_contents(__DIR__ . '/assets/plain.txt'),
            'text/plain'
        );

        $response = $client->postMultipart('http://localhost/upload-file', $multipart);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('plain.txt, 8', $response->getBody());
    }
}
