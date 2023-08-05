<?php
declare(strict_types=1);

namespace Tests\Mock;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\RequestHandler;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

class MockMiddleware extends Middleware
{

    protected bool $loaded = false;

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function process(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        $this->loaded = true;

        return $handler->handle($request);
    }

}
