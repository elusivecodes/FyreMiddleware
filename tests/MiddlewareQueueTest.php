<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Container\Container;
use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\MiddlewareRegistry;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;
use Tests\Mock\MockMiddleware;

use function class_uses;

final class MiddlewareQueueTest extends TestCase
{
    protected MiddlewareRegistry $middlewareRegistry;

    protected MiddlewareQueue $queue;

    public function testAdd(): void
    {
        $this->queue->add(new MockMiddleware());

        $this->assertSame(
            5,
            $this->queue->count()
        );
    }

    public function testCount(): void
    {
        $this->assertSame(
            4,
            $this->queue->count()
        );
    }

    public function testInsertAt(): void
    {
        $middleware = new MockMiddleware();

        $this->queue->insertAt(1, $middleware);
        $this->queue->next();

        $this->assertSame(
            $middleware,
            $this->queue->current()
        );
    }

    public function testIteration(): void
    {
        foreach ($this->queue as $middleware) {
            $this->assertInstanceOf(
                Middleware::class,
                $this->middlewareRegistry->resolve($middleware)
            );
        }
    }

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(MiddlewareQueue::class)
        );
    }

    public function testPrepend(): void
    {
        $middleware = new MockMiddleware();

        $this->queue->prepend($middleware);

        $this->assertSame(
            $middleware,
            $this->queue->current()
        );
    }

    protected function setUp(): void
    {
        $container = new Container();

        $this->middlewareRegistry = $container->build(MiddlewareRegistry::class);
        $this->middlewareRegistry->map('mock', MockMiddleware::class);
        $this->middlewareRegistry->map('mock-closure', fn(): Middleware => new MockMiddleware());

        $this->queue = new MiddlewareQueue([
            new MockMiddleware(),
            MockMiddleware::class,
            'mock',
            'mock-closure',
        ]);
    }
}
