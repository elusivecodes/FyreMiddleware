<?php
declare(strict_types=1);

namespace Fyre\Middleware;

use Fyre\Utility\Traits\MacroTrait;
use Override;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Middleware
 */
abstract class Middleware implements MiddlewareInterface
{
    use MacroTrait;

    /**
     * Process a ServerRequest.
     *
     * @param RequestInterface $request The Request.
     * @param RequestHandlerInterface $handler The RequestHandler.
     * @return ResponseInterface The Response.
     */
    #[Override]
    abstract public function process(RequestInterface $request, RequestHandlerInterface $handler): ResponseInterface;
}
