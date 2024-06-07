<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Closure;
use Countable;
use Iterator;
use OutOfBoundsException;

use function array_key_exists;
use function array_splice;
use function array_unshift;
use function count;

/**
 * MiddlewareQueue
 */
class MiddlewareQueue implements Countable, Iterator
{

    protected int $index = 0;

    protected array $queue = [];

    /**
     * New ClosureMiddleware constructor.
     * @param array $middlewares The middleware.
     */
    public function __construct(array $middlewares = [])
    {
        foreach ($middlewares AS $middleware) {
            $this->add($middleware);
        }
    }

    /**
     * Add Middleware.
     * @param Middleware|Closure|string $middleware The Middleware.
     * @return MiddlewareQueue The MiddlewareQueue.
     */
    public function add(Middleware|Closure|string $middleware): static
    {
        $this->queue[] = MiddlewareRegistry::resolve($middleware);

        return $this;
    }

    /**
     * Get the Middleware count.
     * @return int The Middleware count.
     */
    public function count(): int
    {
        return count($this->queue);
    }

    /**
     * Get the Middleware at the current index.
     * @return Middleware The Middleware at the current index.
     * @throws OutOfBoundsException if the index is out of bounds.
     */
    public function current(): Middleware
    {
        if (!$this->valid()) {
            throw new OutOfBoundsException('Invalid middleware at index: '.$this->index);
        }

        return $this->queue[$this->index];
    }

    /**
     * Insert Middleware at a specified index.
     * @param int $index The index.
     * @param Middleware|Closure|string The Middleware.
     * @return MiddlewareQueue The MiddlewareQueue.
     */
    public function insertAt(int $index, Middleware|Closure|string $middleware): static
    {
        array_splice($this->queue, $index, 0, [MiddlewareRegistry::resolve($middleware)]);

        return $this;
    }

    /**
     * Get the current index.
     * @return int The current index.
     */
    public function key(): int
    {
        return $this->index;
    }

    /**
     * Progress the index.
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * Prepend Middleware.
     * @param Middleware|Closure|string $middleware The Middleware.
     * @return MiddlewareQueue The MiddlewareQueue.
     */
    public function prepend(Middleware|Closure|string $middleware): static
    {
        array_unshift($this->queue, MiddlewareRegistry::resolve($middleware));

        return $this;
    }

    /**
     * Reset the index.
     */
    public function rewind(): void
    {
        $this->index = 0;
    }

    /**
     * Determine if the current index is valid.
     * @return bool TRUE if the current index is valid, otherwise FALSE.
     */
    public function valid(): bool
    {
        return array_key_exists($this->index, $this->queue);
    }

}
