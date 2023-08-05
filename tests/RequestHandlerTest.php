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

    public function testRun()
    {
        $middleware1 = new MockMiddleware();
        $middleware2 = new MockMiddleware();

        $queue = new MiddlewareQueue();
        $queue->add($middleware1);
        $queue->add($middleware2);

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

}
