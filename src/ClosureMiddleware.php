<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Closure;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

/**
 * ClosureMiddleware
 */
class ClosureMiddleware extends Middleware
{
    protected Closure $callback;

    /**
     * New ClosureMiddleware constructor.
     *
     * @param Closure $callback The callback.
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Process a ServerRequest.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @param mixed ...$args Additional arguments to pass to the callback.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, RequestHandler $handler, mixed ...$args): ClientResponse
    {
        return ($this->callback)($request, $handler, ...$args);
    }
}
