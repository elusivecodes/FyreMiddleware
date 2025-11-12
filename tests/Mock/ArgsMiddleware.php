<?php
declare(strict_types=1);

namespace Tests\Mock;

use Fyre\Middleware\Middleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ArgsMiddleware extends Middleware
{
    public function __construct(
        protected int|null $a = null,
        protected int|null $b = null
    ) {}

    public function getArgs(): array
    {
        return [$this->a, $this->b];
    }

    public function process(RequestInterface $request, RequestHandlerInterface $handler, string ...$args): ResponseInterface
    {
        return $handler->handle($request)->withJson($args);
    }
}
