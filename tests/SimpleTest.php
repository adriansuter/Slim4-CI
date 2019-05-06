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
        $response = $client->request('GET', 'http://localhost');

        $this->assertEquals('Hello world!', $response->getBody()->getContents());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
