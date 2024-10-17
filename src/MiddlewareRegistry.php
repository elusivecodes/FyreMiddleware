<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Closure;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use RuntimeException;

use function class_exists;
use function explode;
use function is_string;
use function is_subclass_of;
use function str_contains;

/**
 * MiddlewareRegistry
 */
abstract class MiddlewareRegistry
{
    protected static array $aliases = [];

    protected static array $middleware = [];

    /**
     * Clear all aliases and middleware.
     */
    public static function clear(): void
    {
        static::$aliases = [];
        static::$middleware = [];
    }

    /**
     * Map an alias to middleware.
     *
     * @param string $alias The middleware alias.
     * @param Closure|string The Middleware class, or a function that returns Middleware.
     */
    public static function map(string $alias, Closure|string $middleware): void
    {
        static::$aliases[$alias] = $middleware;
    }

    /**
     * Resolve Middleware.
     *
     * @param Closure|Middleware|string $middleware The Middleware.
     * @return Middleware The Middleware.
     */
    public static function resolve(Closure|Middleware|string $middleware): Middleware
    {
        if ($middleware instanceof Middleware) {
            return $middleware;
        }

        if ($middleware instanceof Closure) {
            return new ClosureMiddleware($middleware);
        }

        if (str_contains($middleware, ':')) {
            [$alias, $args] = explode(':', $middleware, 2);
            $args = explode(',', $args);

            return new ClosureMiddleware(fn(ServerRequest $request, RequestHandler $handler): ClientResponse => Closure::fromCallable([static::use($alias), 'process'])($request, $handler, ...$args));
        }

        return static::use($middleware);
    }

    /**
     * Load a shared Middleware instance.
     *
     * @param string $alias The middleware alias.
     * @return Middleware The Middleware.
     */
    public static function use(string $alias): Middleware
    {
        return static::$middleware[$alias] ??= static::load($alias);
    }

    /**
     * Load a Middleware.
     *
     * @param string $alias The middleware alias.
     * @return Middleware The Middleware.
     */
    protected static function load(string $alias): Middleware
    {
        $middleware = static::$aliases[$alias] ?? $alias;

        if (is_string($middleware) && class_exists($middleware) && is_subclass_of($middleware, Middleware::class)) {
            return new $middleware();
        }

        if ($middleware instanceof Closure) {
            return $middleware();
        }

        throw new RuntimeException('Invalid middleware: '.$middleware);
    }
}
