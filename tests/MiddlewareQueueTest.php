<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Middleware\Middleware,
    Fyre\Middleware\MiddlewareQueue,
    PHPUnit\Framework\TestCase,
    Tests\Mock\MockMiddleware;

final class MiddlewareQueueTest extends TestCase
{

    protected MiddlewareQueue $queue;

    public function testCount()
    {
        $this->assertEquals(
            2,
            $this->queue->count()
        );
    }

    public function testIteration()
    {
        foreach ($this->queue AS $middleware) {
            $this->assertInstanceOf(
                Middleware::class,
                $middleware
            );
        }
    }

    public function testInsertAt()
    {
        $middleware = new MockMiddleware;

        $this->queue->insertAt(1, $middleware);
        $this->queue->next();

        $this->assertEquals(
            $middleware,
            $this->queue->current()
        );
    }

    public function testPrepend()
    {
        $middleware = new MockMiddleware;

        $this->queue->prepend($middleware);

        $this->assertEquals(
            $middleware,
            $this->queue->current()
        );
    }

    protected function setUp(): void
    {
        $this->queue = new MiddlewareQueue();

        $this->queue->add(new MockMiddleware);
        $this->queue->add(new MockMiddleware);
    }

}
