<?php
declare(strict_types=1);

namespace Tests\Mock;

use Closure;
use Fyre\Middleware\Middleware;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

class ArgsMiddleware extends Middleware
{
    protected int|null $a = null;

    protected int|null $b = null;

    public function __construct(int|null $a = null, int|null $b = null)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function getArgs(): array
    {
        return [$this->a, $this->b];
    }

    public function handle(ServerRequest $request, Closure $next, string ...$args): ClientResponse
    {
        return $next($request)->setJson($args);
    }
}
