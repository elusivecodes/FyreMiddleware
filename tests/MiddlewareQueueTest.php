<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\MiddlewareRegistry;
use PHPUnit\Framework\TestCase;
use Tests\Mock\MockMiddleware;

final class MiddlewareQueueTest extends TestCase
{
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
                $middleware
            );
        }
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
        MiddlewareRegistry::clear();
        MiddlewareRegistry::map('mock', MockMiddleware::class);
        MiddlewareRegistry::map('mock-closure', fn(): Middleware => new MockMiddleware());

        $this->queue = new MiddlewareQueue([
            new MockMiddleware(),
            MockMiddleware::class,
            'mock',
            'mock-closure',
        ]);
    }
}
