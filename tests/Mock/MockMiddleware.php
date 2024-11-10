<?php
declare(strict_types=1);

namespace Tests\Mock;

use Closure;
use Fyre\Middleware\Middleware;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

class MockMiddleware extends Middleware
{
    protected bool $loaded = false;

    public function __invoke(ServerRequest $request, Closure $next): ClientResponse
    {
        $this->loaded = true;

        return $next($request);
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }
}
