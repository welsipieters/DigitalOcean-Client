<?php

declare(strict_types=1);

namespace DigitalOceanV2\Tests;

use DigitalOceanV2\Exception\RuntimeException;
use DigitalOceanV2\HttpClient\Message\Response;
use DigitalOceanV2\HttpClient\Message\ResponseMediator;
use PHPUnit\Framework\TestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 */
class ResponseMediatorTest extends TestCase
{
    public function testGetContent(): void
    {
        $response = new Response(
            200,
            'OK',
            ['Content-Type' => ['application/json']],
            '{"foo": "bar"}'
        );

        $this->assertEquals((object) ['foo' => 'bar'], ResponseMediator::getContent($response));
    }

    public function testGetContentNotJson(): void
    {
        $body = 'foobar';
        $response = new Response(
            200,
            'OK',
            ['Content-Type' => ['text/html']],
            $body
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The content type was not application/json.');

        ResponseMediator::getContent($response);
    }

    public function testGetContentInvalidJson(): void
    {
        $body = 'foobar';
        $response = new Response(
            200,
            'OK',
            ['Content-Type' => ['application/json']],
            $body
        );

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('json_decode error: Syntax error');

        ResponseMediator::getContent($response);
    }

    public function testGetErrrorMessageInvalidJson(): void
    {
        $body = 'foobar';
        $response = new Response(
            200,
            'OK',
            ['Content-Type' => ['application/json']],
            $body
        );

        $this->assertNull(ResponseMediator::getErrorMessage($response));
    }
}
