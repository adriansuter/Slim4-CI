<?php

declare(strict_types=1);

namespace Slim\Tests\CI;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testGet()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello world!', $response->getBody()->getContents());
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

        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/psr-7');

        $this->assertEquals(
            $expectedBodyContents[$psr7],
            $response->getBody()->getContents()
        );
    }

    public function testGetPlaceholder()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/hello/slim');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello slim!', $response->getBody()->getContents());
    }

    public function testGetQueryParams()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/query-params', [
            'query' => ['foo' => 'bar', 'white space' => 'hello world']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foo:bar|white_space:hello world', $response->getBody()->getContents());
    }

    public function testGetWithStatus()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/status-202');

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals('Status 202', $response->getBody()->getContents());
        $this->assertEqualsIgnoringCase('accepted', $response->getReasonPhrase());
    }

    public function testGetWithStatusAndReasonPhrase()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/status-reason-phrase');

        $this->assertEquals(299, $response->getStatusCode());
        $this->assertEqualsIgnoringCase('peace', $response->getReasonPhrase());
        $this->assertEquals('Status 299 - Peace', $response->getBody()->getContents());
    }

    public function testGetRequestTarget()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/request-target');

        $this->assertEquals('/request-target', $response->getBody()->getContents());
    }

    public function testGetMethod()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/method');

        $this->assertEquals('GET', $response->getBody()->getContents());
    }

    public function testGetUri()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/uri?foo=bar#section');

        $this->assertEquals('http, localhost, localhost, , /uri, , foo=bar, ', $response->getBody()->getContents());
    }

    public function testGetRedirect()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('GET', 'http://localhost/redirect');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Redirected', $response->getBody()->getContents());
    }

    public function testPostFormData()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('POST', 'http://localhost/form-data', [
            'form_params' => [
                'foo' => 'bar',
                'multi' => [
                    '9',
                    '8',
                ],
            ],
        ]);

        $this->assertEquals('{"foo":"bar","multi":["9","8"]}', $response->getBody()->getContents());
    }

    public function testPostFileUpload()
    {
        $client = new Client();
        /** @noinspection PhpUnhandledExceptionInspection */
        $response = $client->request('POST', 'http://localhost/upload-file', [
            'multipart' => [
                [
                    'name' => 'upload',
                    'filename' => 'plain.txt',
                    'contents' => fopen(__DIR__ . '/assets/plain.txt', 'r')
                ],
            ],
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('plain.txt, 8', $response->getBody()->getContents());
    }
}
