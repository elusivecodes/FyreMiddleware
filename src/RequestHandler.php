<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Fyre\Container\Container;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Fyre\Utility\Traits\MacroTrait;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * RequestHandler
 */
class RequestHandler implements RequestHandlerInterface
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
        protected ResponseInterface|null $initialResponse = null
    ) {}

    /**
     * Handle the next middleware in the queue.
     *
     * @param ServerRequestInterface $request The ServerRequestInterface.
     * @return ResponseInterface The ClientResponse.
     */
    #[Override]
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request instanceof ServerRequest) {
            $this->container->instance(ServerRequest::class, $request);
        }

        if (!$this->queue->valid()) {
            return $this->initialResponse ?? $this->container->build(ClientResponse::class);
        }

        $middleware = $this->middlewareRegistry->resolve($this->queue->current());

        $this->queue->next();

        if ($middleware instanceof Middleware) {
            $middleware = $middleware->process(...);
        }

        return $middleware($request, $this);
    }
}
