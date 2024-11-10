<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Container\Container;
use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareRegistry;
use PHPUnit\Framework\TestCase;
use Tests\Mock\ArgsMiddleware;
use Tests\Mock\MockMiddleware;

final class MiddlewareRegistryTest extends TestCase
{
    protected MiddlewareRegistry $middlewareRegistry;

    public function testMapClassString()
    {
        $this->middlewareRegistry->map('mock', MockMiddleware::class);

        $this->assertInstanceOf(
            MockMiddleware::class,
            $this->middlewareRegistry->use('mock')
        );
    }

    public function testMapClassStringArguments()
    {
        $this->middlewareRegistry->map('mock', ArgsMiddleware::class, [
            'a' => 1,
            'b' => 2,
        ]);

        $middleware = $this->middlewareRegistry->use('mock');

        $this->assertInstanceOf(
            ArgsMiddleware::class,
            $middleware
        );

        $this->assertSame(
            [1, 2],
            $middleware->getArgs()
        );
    }

    public function testMapClosure()
    {
        $this->middlewareRegistry->map('mock', fn(): Middleware => new MockMiddleware());

        $this->assertInstanceOf(
            MockMiddleware::class,
            $this->middlewareRegistry->use('mock')
        );
    }

    public function testMapClosureArgs()
    {
        $this->middlewareRegistry->map('mock', fn(int $a, int $b): Middleware => new ArgsMiddleware($a, $b), [
            'a' => 1,
            'b' => 2,
        ]);

        $middleware = $this->middlewareRegistry->use('mock');

        $this->assertInstanceOf(
            ArgsMiddleware::class,
            $middleware
        );

        $this->assertSame(
            [1, 2],
            $middleware->getArgs()
        );
    }

    public function testUseClassString()
    {
        $this->assertInstanceOf(
            MockMiddleware::class,
            $this->middlewareRegistry->use(MockMiddleware::class)
        );
    }

    protected function setUp(): void
    {
        $container = new Container();

        $this->middlewareRegistry = $container->build(MiddlewareRegistry::class);
    }
}
