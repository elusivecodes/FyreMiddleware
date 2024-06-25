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
    protected ClientResponse $initialResponse;

    protected MiddlewareQueue $queue;

    /**
     * New RequestHandler constructor.
     *
     * @param MiddlewareQueue $queue The MiddlewareQueue.
     */
    public function __construct(MiddlewareQueue $queue, ClientResponse|null $initialResponse = null)
    {
        $this->queue = $queue;
        $this->initialResponse = $initialResponse ?? new ClientResponse();
    }

    /**
     * Handle the next middleware in the queue.
     *
     * @param ServerRequest $request The ServerRequest.
     * @return ClientResponse The ClientResponse.
     */
    public function handle(ServerRequest $request): ClientResponse
    {
        if (!$this->queue->valid()) {
            return $this->initialResponse;
        }

        $middleware = $this->queue->current();
        $this->queue->next();

        return $middleware->process($request, $this);
    }
}
