<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareRegistry;
use PHPUnit\Framework\TestCase;
use Tests\Mock\MockMiddleware;

final class MiddlewareRegistryTest extends TestCase
{
    public function testMapClassString()
    {
        MiddlewareRegistry::map('mock', MockMiddleware::class);

        $this->assertInstanceOf(
            MockMiddleware::class,
            MiddlewareRegistry::use('mock')
        );
    }

    public function testMapClosure()
    {
        MiddlewareRegistry::map('mock', fn(): Middleware => new MockMiddleware());

        $this->assertInstanceOf(
            MockMiddleware::class,
            MiddlewareRegistry::use('mock')
        );
    }

    public function testUseClassString()
    {
        $this->assertInstanceOf(
            MockMiddleware::class,
            MiddlewareRegistry::use(MockMiddleware::class)
        );
    }

    protected function setUp(): void
    {
        MiddlewareRegistry::clear();
    }
}
