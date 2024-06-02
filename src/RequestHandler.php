<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

/**
 * RequestHandler
 */
class RequestHandler
{

    protected MiddlewareQueue $queue;

    /**
     * New RequestHandler constructor.
     * @param MiddlewareQueue $queue The MiddlewareQueue.
     */
    public function __construct(MiddlewareQueue $queue)
    {
        $this->queue = $queue;
    }

    /**
     * Handle the next middleware in the queue.
     * @param ServerRequest $request The ServerRequest.
     * @return ClientResponse The ClientResponse.
     */
    public function handle(ServerRequest $request): ClientResponse
    {
        if (!$this->queue->valid()) {
            return new ClientResponse();
        }

        $middleware = $this->queue->current();
        $this->queue->next();

        return $middleware($request, $this);
    }

}
