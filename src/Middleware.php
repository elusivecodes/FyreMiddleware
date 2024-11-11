<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Closure;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

/**
 * Middleware
 */
abstract class Middleware
{
    /**
     * Handle a ServerRequest.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param Closure $next The next handler.
     * @return ClientResponse The ClientResponse.
     */
    abstract public function handle(ServerRequest $request, Closure $next): ClientResponse;
}
