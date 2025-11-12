<?php
declare(strict_types=1);

namespace Tests\Mock;

use Fyre\Middleware\Middleware;
use Fyre\Server\ClientResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MockMiddleware extends Middleware
{
    protected bool $loaded = false;

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ClientResponse
    {
        $this->loaded = true;

        return $handler->handle($request);
    }
}
