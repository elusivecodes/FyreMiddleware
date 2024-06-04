<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Closure;
use RuntimeException;

use function class_exists;
use function is_string;
use function is_subclass_of;

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
     * @param string $alias The middleware alias.
     * @param Closure|string The Middleware class, or a function that returns Middleware.
     */
    public static function map(string $alias, Closure|string $middleware): void
    {
        static::$aliases[$alias] = $middleware;
    }

    /**
     * Load a shared Middleware instance.
     * @param string $alias The middleware alias.
     * @return Middleware The Middleware.
     */
    public static function use(string $alias): Middleware
    {
        return static::$middleware[$alias] ??= static::load($alias);
    }

    /**
     * Load a Middleware.
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
