<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest;

/**
 * Middleware
 */
abstract class Middleware
{

    /**
     * Process a ServerRequest.
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    abstract public function process(ServerRequest $request, RequestHandler $handler): ClientResponse;

}
