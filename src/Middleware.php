<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

/**
 * Middleware
 */
abstract class Middleware
{

    /**
     * Invoke the middleware.
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    public function __invoke(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        return $this->process($request, $handler);
    }

    /**
     * Process a ServerRequest.
     * @param ServerRequest $request The ServerRequest.
     * @param RequestHandler $handler The RequestHandler.
     * @return ClientResponse The ClientResponse.
     */
    abstract public function process(ServerRequest $request, RequestHandler $handler): ClientResponse;

}
