<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Fyre\Container\Container;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Fyre\Utility\Traits\MacroTrait;

/**
 * RequestHandler
 */
class RequestHandler
{
    use MacroTrait;

    /**
     * New RequestHandler constructor.
     *
     * @param MiddlewareQueue $queue The MiddlewareQueue.
     */
    public function __construct(
        protected Container $container,
        protected MiddlewareRegistry $middlewareRegistry,
        protected MiddlewareQueue $queue,
        protected ClientResponse|null $initialResponse = null
    ) {}

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

        $middleware = $this->middlewareRegistry->resolve($this->queue->current());

        $this->queue->next();

        if ($middleware instanceof Middleware) {
            $middleware = $middleware->handle(...);
        }

        return $middleware(
            $request,
            fn(ServerRequest $request): ClientResponse => $this->handle($request)
        );
    }
}
