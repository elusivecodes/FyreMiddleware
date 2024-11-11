# FyreMiddleware

**FyreMiddleware** is a free, open-source middleware library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Basic Usage](#basic-usage)
- [Methods](#methods)
- [Middleware Queues](#middleware-queues)
    - [Middleware](#middleware)
- [Request Handlers](#request-handlers)



## Installation

**Using Composer**

```
composer require fyre/middleware
```

In PHP:

```php
use Fyre\Middleware\MiddlewareRegistry;
```


## Basic Usage

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$middlewareRegistry = new MiddlewareRegistry($container);
```

It is recommended to bind the *MiddlewareRegistry* to the [*Container*](https://github.com/elusivecodes/FyreContainer) as a singleton.

```php
$container->singleton(MiddlewareRegistry::class);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$middlewareRegistry = $container->use(MiddlewareRegistry::class);
```


## Methods

**Clear**

Clear all aliases and middleware.

```php
$middlewareRegistry->clear();
```

**Map**

Map an alias to middleware.

- `$alias` is a string representing the middleware alias.
- `$middleware` is a string representing the [*Middleware*](#middleware) class name, or a closure that returns an instance of a [*Middleware*](middleware) class.
- `$arguments` is an array containing additional arguments for creating the [*Middleware*](#middleware), and will default to *[]*.

```php
$middlewareRegistry->map($alias, $middleware, $arguments);
```

**Resolve**

Resolve [*Middleware*](#middleware).

- `$middleware` is a [*Middleware*](#middleware) class instance, class name, alias or *Closure*.

```php
$resolvedMiddleware = $middlewareRegistry->resolve($middleware);
```

You can pass additional arguments to the `handle` method of the [*Middleware*](#middleware) by appending a colon followed by a comma-separated list of arguments to the string.

```php
$middlewareRegistry->resolve('alias:arg1,arg2');
```

[*Middleware*](#middleware) dependencies will be resolved automatically from the [*Container*](https://github.com/elusivecodes/FyreContainer).

**Use**

Load a shared [*Middleware*](#middleware) instance.

- `$alias` is a string representing the middleware alias.

```php
$middleware = $middlewareRegistry->use($alias);
```

[*Middleware*](#middleware) dependencies will be resolved automatically from the [*Container*](https://github.com/elusivecodes/FyreContainer).


## Middleware Queues

```php
use Fyre\Middleware\MiddlewareQueue;
```

- `$middlewares` is an array containing the [*Middleware*](#middleware).

```php
$queue = new MiddlewareQueue($middlewares);
```

**Add**

Add [*Middleware*](#middleware).

- `$middleware` is a [*Middleware*](#middleware) class instance, class name name, alias or [*Closure*](#closures).

```php
$queue->add($middleware);
```

**Count**

Get the [*Middleware*](#middleware) count.

```php
$count = $queue->count();
```

**Current**

Get the [*Middleware*](#middleware) at the current index.

```php
$middleware = $queue->current();
```

**Insert At**

Insert [*Middleware*](#middleware) at a specified index.

- `$index` is a number representing the index.
- `$middleware` is a [*Middleware*](#middleware) class instance, class name name, alias or [*Closure*](#closures).

```php
$queue->insertAt($index, $middleware);
```

**Key**

Get the current index.

```php
$key = $queue->key();
```

**Next**

Progress the index.

```php
$queue->next();
```

**Prepend**

Prepend [*Middleware*](#middleware).

- `$middleware` is a [*Middleware*](#middleware) class instance, class name name, alias or [*Closure*](#closures).

```php
$queue->prepend($middleware);
```

**Rewind**

Reset the index.

```php
$queue->rewind();
```

**Valid**

Determine whether the current index is valid.

```php
$valid = $queue->valid();
```


### Middleware

Custom middleware can be created by extending `\Fyre\Middleware\Middleware`, ensuring all below methods are implemented.

**Handle**

Handle a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$next` is a *Closure*.

```php
$response = $middleware->handle($request, $next);
```

This method should call the `$next` callback with the `$request`, to handle the next middleware in the queue, then return the [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).


### Closures

You can also provide custom middleware as a simple *Closure*.

```php
$middleware = function(ServerRequest $request, Closure $next): ClientResponse {
    return $next($request);
};
```


## Request Handlers

```php
use Fyre\Middleware\RequestHandler;
```

- `$container` is a [*Container*](https://github.com/elusivecodes/FyreContainer).
- `$middlewareRegistry` is a [*MiddlewareRegistry*](#basic-usage).
- `$queue` is a [*MiddlewareQueue*](#middleware-queues).
- `$initialResponse` is a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses) to be used as the initial response, and will default to *null*.

```php
$handler = new RequestHandler($container, $middlewareRegistry, $queue, $initialResponse);
```

Any dependencies will be injected automatically when loading from the [*Container*](https://github.com/elusivecodes/FyreContainer).

```php
$handler = $container->use(RequestHandler::class, 'queue' => $queue);
```

If the `$initialResponse` is set to *null*, a new [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses) will be created.

**Handle**

Handle the next middleware in the queue.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).

```php
$response = $handler->handle($request);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

The provided `$request` will be automatically set as the [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests) instance in the [*Container*](https://github.com/elusivecodes/FyreContainer).