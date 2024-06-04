<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Closure;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function call_user_func_array;

/**
 * ClosureMiddleware
 */
class ClosureMiddleware extends Middleware
{

    protected Closure $callback;

    /**
     * New ClosureMiddleware constructor.
     * @param Closure $callback The callback.
     */
    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Process a ServerRequest.
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    public function process(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        return call_user_func_array($this->callback, [$request, $handler]);
    }

}
