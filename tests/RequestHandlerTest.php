<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tests\Mock\MockMiddleware;

final class RequestHandlerTest extends TestCase
{

    public function testRun(): void
    {
        $middleware1 = new MockMiddleware();
        $middleware2 = new MockMiddleware();

        $queue = new MiddlewareQueue([
            $middleware1,
            $middleware2
        ]);

        $handler = new RequestHandler($queue);
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
    }

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



}
