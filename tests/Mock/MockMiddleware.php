<?php
declare(strict_types=1);

namespace Tests\Mock;

use
    Fyre\Middleware\Middleware,
    Fyre\Middleware\RequestHandler,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest;

class MockMiddleware extends Middleware
{

    protected bool $loaded = false;

    public function loaded(): bool
    {
        return $this->loaded;
    }

    public function process(ServerRequest $request, RequestHandler $handler): ClientResponse
    {
        $this->loaded = true;

        return $handler->handle($request);
    }

}
