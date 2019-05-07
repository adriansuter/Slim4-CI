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
        $response = $client->request('GET', 'http://localhost/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello world!', $response->getBody()->getContents());
    }

    public function testGetPsr7()
    {
        $expectedBodyContents = [
            'slim/psr7'
            => 'Slim\Psr7\Request, Slim\Psr7\Response',
            'nyholm/psr7 nyholm/psr7-server'
            => 'Nyholm\Psr7\ServerRequest, Nyholm\Psr7\Response',
            'guzzlehttp/psr7 http-interop/http-factory-guzzle'
            => 'GuzzleHttp\Psr7\ServerRequest, GuzzleHttp\Psr7\Response',
            'zendframework/zend-diactoros'
            => 'Zend\Diactoros\ServerRequest, Zend\Diactoros\Response',
        ];
        
        $composerPsr7 = getenv('COMPOSER_PSR7');
        if ($composerPsr7 === false || !isset($expectedBodyContents[$composerPsr7])) {
            $this->markTestSkipped();
            return;
        }

        $client = new Client();
        $response = $client->request('GET', 'http://localhost/psr-7');

        $this->assertEquals(
            $expectedBodyContents[$composerPsr7],
            $response->getBody()->getContents()
        );
    }

    public function testGetPlaceholder()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/hello/slim');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello slim!', $response->getBody()->getContents());
    }

    public function testGetQueryParams()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/query-params', [
            'query' => ['foo' => 'bar', 'white space' => 'hello world']
        ]);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('foo:bar|white_space:hello world', $response->getBody()->getContents());
    }

    public function testGetWithStatus()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/status-202');

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals('Status 202', $response->getBody()->getContents());
        $this->assertEqualsIgnoringCase('accepted', $response->getReasonPhrase());
    }

    public function testGetWithStatusAndReasonPhrase()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/status-reason-phrase');

        $this->assertEquals(299, $response->getStatusCode());
        $this->assertEqualsIgnoringCase('peace', $response->getReasonPhrase());
        $this->assertEquals('Status 299 - Peace', $response->getBody()->getContents());
    }

    public function testGetRedirect()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/redirect');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Redirected', $response->getBody()->getContents());
    }

    public function testPostFormData()
    {
        $client = new Client();
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
}
