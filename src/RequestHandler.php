<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Fyre\Container\Container;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

/**
 * RequestHandler
 */
class RequestHandler
{
    protected Container $container;

    protected ClientResponse $initialResponse;

    protected MiddlewareRegistry $middlewareRegistry;

    protected MiddlewareQueue $queue;

    /**
     * New RequestHandler constructor.
     *
     * @param MiddlewareQueue $queue The MiddlewareQueue.
     */
    public function __construct(Container $container, MiddlewareRegistry $middlewareRegistry, MiddlewareQueue $queue, ClientResponse|null $initialResponse = null)
    {
        $this->container = $container;
        $this->middlewareRegistry = $middlewareRegistry;
        $this->queue = $queue;
        $this->initialResponse = $initialResponse;
    }

    /**
     * Handle the next middleware in the queue.
     *
     * @param ServerRequest $request The ServerRequest.
     * @return ClientResponse The ClientResponse.
     */
    public function handle(ServerRequest $request): ClientResponse
    {
        $this->container->instance(ServerRequest::class, $request);

        if (!$this->queue->valid()) {
            return $this->initialResponse ?? $this->container->build(ClientResponse::class);
        }

        $middleware = $this->queue->current();
        $this->queue->next();

        return $this->middlewareRegistry->resolve($middleware)->process($request, $this);
    }
}
