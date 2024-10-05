<?php
declare(strict_types=1);

namespace Tests\Mock;

use Fyre\Middleware\Middleware;
use Fyre\Middleware\RequestHandler;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

class ArgsMiddleware extends Middleware
{
    public function process(ServerRequest $request, RequestHandler $handler, string ...$args): ClientResponse
    {
        return $handler->handle($request)->setJson($args);
    }
}
