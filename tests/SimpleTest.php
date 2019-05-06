<?php

declare(strict_types=1);

namespace Slim\Tests\CI;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function testGetRoot()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello world!', $response->getBody()->getContents());
    }

    public function testPlaceholder()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/hello/slim');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello slim!', $response->getBody()->getContents());
    }

    public function testWithStatus()
    {
        $client = new Client();
        $response = $client->request('GET', 'http://localhost/status-202');

        $this->assertEquals(202, $response->getStatusCode());
        $this->assertEquals('Status 202', $response->getBody()->getContents());
        $this->assertEqualsIgnoringCase('accepted', $response->getReasonPhrase());
    }
}
