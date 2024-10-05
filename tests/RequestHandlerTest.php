<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\MiddlewareRegistry;
use Fyre\Middleware\RequestHandler;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tests\Mock\ArgsMiddleware;
use Tests\Mock\MockMiddleware;

final class RequestHandlerTest extends TestCase
{
    public function testInitialResponse(): void
    {
        $queue = new MiddlewareQueue();

        $response = new ClientResponse();
        $handler = new RequestHandler($queue, $response);
        $request = new ServerRequest();

        $this->assertSame(
            $response,
            $handler->handle($request)
        );
    }

    public function testRun(): void
    {
        $middleware1 = new MockMiddleware();
        $middleware2 = new MockMiddleware();

        $queue = new MiddlewareQueue([
            $middleware1,
            $middleware2,
        ]);

        $i = 0;
        $handler = new RequestHandler($queue, null, function(ServerRequest $request) use (&$i): void {
            $i++;
        });
        $request = new ServerRequest();

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );

        $this->assertTrue(
            $middleware1->isLoaded()
        );

        $this->assertTrue(
            $middleware2->isLoaded()
        );

        $this->assertSame(
            3,
            $i
        );
    }

    public function testRunMapClosureWithArgs()
    {
        MiddlewareRegistry::map('mock', fn(): Middleware => new ArgsMiddleware());

        $queue = new MiddlewareQueue([
            'mock:1,2,3',
        ]);

        $handler = new RequestHandler($queue);
        $request = new ServerRequest();

        $response = $handler->handle($request);

        $this->assertEquals(
            '[
    "1",
    "2",
    "3"
]',
            $response->getBody()
        );
    }

    public function testRunMapWithArgs()
    {
        MiddlewareRegistry::map('mock', ArgsMiddleware::class);

        $queue = new MiddlewareQueue([
            'mock:1,2,3',
        ]);

        $handler = new RequestHandler($queue);
        $request = new ServerRequest();

        $response = $handler->handle($request);

        $this->assertEquals(
            '[
    "1",
    "2",
    "3"
]',
            $response->getBody()
        );
    }
}
