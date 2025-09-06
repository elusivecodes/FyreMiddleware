<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Closure;
use Fyre\Container\Container;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Fyre\Utility\Traits\MacroTrait;
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
            $this->aliases[$alias] = fn(): Closure|Middleware => $this->container->call($middleware, $arguments);
        } else if ($arguments !== []) {
            $this->aliases[$alias] = fn(): Closure|Middleware => $this->container->build($middleware, $arguments);
        } else {
            $this->aliases[$alias] = $middleware;
        }

        unset($this->middleware[$alias]);

        return $this;
    }

    /**
     * Resolve Middleware.
     *
     * @param Closure|Middleware|string $middleware The Middleware.
     * @return Closure|Middleware The Middleware.
     */
    public function resolve(Closure|Middleware|string $middleware): Closure|Middleware
    {
        if (is_string($middleware)) {
            if (!str_contains($middleware, ':')) {
                return $this->use($middleware);
            }

            [$alias, $args] = explode(':', $middleware, 2);
            $middleware = $this->use($alias);
            $args = explode(',', $args);

            if ($middleware instanceof Middleware) {
                $middleware = $middleware->handle(...);
            }

            return fn(ServerRequest $request, Closure $next): ClientResponse => $middleware($request, $next, ...$args);
        }

        return $middleware;
    }

    /**
     * Load a shared Middleware instance.
     *
     * @param string $alias The middleware alias.
     * @return Closure|Middleware The Middleware.
     */
    public function use(string $alias): Closure|Middleware
    {
        return $this->middleware[$alias] ??= $this->build($alias);
    }

    /**
     * Build a Middleware.
     *
     * @param string $alias The middleware alias.
     * @return Closure|Middleware The Middleware.
     */
    protected function build(string $alias): Closure|Middleware
    {
        $middleware = $this->aliases[$alias] ?? $alias;

        if (is_string($middleware) && class_exists($middleware) && is_subclass_of($middleware, Middleware::class)) {
            return $this->container->build($middleware);
        }

        if ($middleware instanceof Closure) {
            return $middleware();
        }

        throw new RuntimeException('Invalid middleware: '.$middleware);
    }
}
