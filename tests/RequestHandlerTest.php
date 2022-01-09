<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Middleware\MiddlewareQueue,
    Fyre\Middleware\RequestHandler,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest,
    PHPUnit\Framework\TestCase,
    Tests\Mock\MockMiddleware;

final class RequestHandlerTest extends TestCase
{

    public function testRun()
    {
        $middleware1 = new MockMiddleware;
        $middleware2 = new MockMiddleware;

        $queue = new MiddlewareQueue;
        $queue->add($middleware1);
        $queue->add($middleware2);

        $handler = new RequestHandler($queue);
        $request = new ServerRequest;

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );

        $this->assertTrue(
            $middleware1->loaded()
        );

        $this->assertTrue(
            $middleware2->loaded()
        );
    }

}
