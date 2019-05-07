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
