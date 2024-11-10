<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Container\Container;
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
    protected Container $container;

    protected MiddlewareRegistry $middlewareRegistry;

    public function testInitialResponse(): void
    {
        $queue = new MiddlewareQueue();

        $response = new ClientResponse();
        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue, 'initialResponse' => $response]);
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

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
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
            $request,
            $this->container->use(ServerRequest::class)
        );
    }

    public function testRunMapClosureWithArgs()
    {
        $this->middlewareRegistry->map('mock', fn(): Middleware => new ArgsMiddleware());

        $queue = new MiddlewareQueue([
            'mock:1,2,3',
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
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
        $this->middlewareRegistry->map('mock', ArgsMiddleware::class);

        $queue = new MiddlewareQueue([
            'mock:1,2,3',
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
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

    protected function setUp(): void
    {
        $this->container = new Container();
        $this->container->singleton(MiddlewareRegistry::class);

        $this->middlewareRegistry = $this->container->use(MiddlewareRegistry::class);
    }
}
