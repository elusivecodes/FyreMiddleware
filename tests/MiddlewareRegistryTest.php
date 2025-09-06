<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Container\Container;
use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareRegistry;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;
use Tests\Mock\ArgsMiddleware;
use Tests\Mock\MockMiddleware;

use function class_uses;

final class MiddlewareRegistryTest extends TestCase
{
    protected MiddlewareRegistry $middlewareRegistry;

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(MiddlewareRegistry::class)
        );

        $this->assertContains(
            MacroTrait::class,
            class_uses(Middleware::class)
        );
    }

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
