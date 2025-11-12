<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Closure;
use Fyre\Container\Container;
use Fyre\Utility\Traits\MacroTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RuntimeException;

use function class_exists;
use function explode;
use function is_string;
use function is_subclass_of;
use function str_contains;

/**
 * MiddlewareRegistry
 */
class MiddlewareRegistry
{
    use MacroTrait;

    protected array $aliases = [];

    protected array $middleware = [];

    /**
     * New MiddlewareRegistry constructor.
     *
     * @param Container $container The Container.
     */
    public function __construct(
        protected Container $container
    ) {}

    /**
     * Clear all aliases and middleware.
     */
    public function clear(): void
    {
        $this->aliases = [];
        $this->middleware = [];
    }

    /**
     * Map an alias to middleware.
     *
     * @param string $alias The middleware alias.
     * @param Closure|string $middleware The Middleware class, or a function that returns Middleware.
     * @param array $arguments Additional arguments for the Middleware.
     * @return MiddlewareRegistry The MiddlewareRegistry.
     */
    public function map(string $alias, Closure|string $middleware, array $arguments = []): static
    {
        if (!is_string($middleware)) {
            $this->aliases[$alias] = fn(): Closure|MiddlewareInterface => $this->container->call($middleware, $arguments);
        } else if ($arguments !== []) {
            $this->aliases[$alias] = fn(): Closure|MiddlewareInterface => $this->container->build($middleware, $arguments);
        } else {
            $this->aliases[$alias] = $middleware;
        }

        unset($this->middleware[$alias]);

        return $this;
    }

    /**
     * Resolve Middleware.
     *
     * @param Closure|MiddlewareInterface|string $middleware The Middleware.
     * @return Closure|MiddlewareInterface The Middleware.
     */
    public function resolve(Closure|MiddlewareInterface|string $middleware): Closure|MiddlewareInterface
    {
        if (is_string($middleware)) {
            if (!str_contains($middleware, ':')) {
                return $this->use($middleware);
            }

            [$alias, $args] = explode(':', $middleware, 2);
            $middleware = $this->use($alias);
            $args = explode(',', $args);

            if ($middleware instanceof Middleware) {
                $middleware = $middleware->process(...);
            }

            return static fn(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface => $middleware($request, $handler, ...$args);
        }

        return $middleware;
    }

    /**
     * Load a shared Middleware instance.
     *
     * @param string $alias The middleware alias.
     * @return Closure|MiddlewareInterface The Middleware.
     */
    public function use(string $alias): Closure|MiddlewareInterface
    {
        return $this->middleware[$alias] ??= $this->build($alias);
    }

    /**
     * Build a Middleware.
     *
     * @param string $alias The middleware alias.
     * @return Closure|MiddlewareInterface The Middleware.
     */
    protected function build(string $alias): Closure|MiddlewareInterface
    {
        $middleware = $this->aliases[$alias] ?? $alias;

        if (is_string($middleware) && class_exists($middleware) && is_subclass_of($middleware, MiddlewareInterface::class)) {
            return $this->container->build($middleware);
        }

        if ($middleware instanceof Closure) {
            return $middleware();
        }

        throw new RuntimeException('Invalid middleware: '.$middleware);
    }
}
