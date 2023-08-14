<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareQueue;
use PHPUnit\Framework\TestCase;
use Tests\Mock\MockMiddleware;

final class MiddlewareQueueTest extends TestCase
{

    protected MiddlewareQueue $queue;

    public function testCount(): void
    {
        $this->assertSame(
            2,
            $this->queue->count()
        );
    }

    public function testIteration(): void
    {
        foreach ($this->queue AS $middleware) {
            $this->assertInstanceOf(
                Middleware::class,
                $middleware
            );
        }
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
        $this->queue = new MiddlewareQueue();

        $this->queue->add(new MockMiddleware());
        $this->queue->add(new MockMiddleware());
    }

}
