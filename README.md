# FyreMiddleware

**FyreMiddleware** is a free, open-source middleware library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Middleware Queues](#middleware-queues)
    - [Middleware](#middleware)
- [Middleware Registry](#middleware-registry)
- [Request Handlers](#request-handlers)



## Installation

**Using Composer**

```
composer require fyre/middleware
```

In PHP:

```php
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
```


## Middleware Queues

```php
$queue = new MiddlewareQueue();
```

**Add**

Add [*Middleware*](#middleware).

- `$middleware` is a [*Middleware*](#middleware) class instance or name, *Closure* or a [*Middleware Registry*](#middleware-registry) alias.

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
- `$middleware` is a [*Middleware*](#middleware) class instance or name, *Closure* or a [*Middleware Registry*](#middleware-registry) alias.

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

- `$middleware` is a [*Middleware*](#middleware) class instance or name, *Closure* or a [*Middleware Registry*](#middleware-registry) alias.

```php
$queue->prepend($middleware);
```

**Rewind**

Reset the index.

```php
$queue->rewind();
```

**Valid**

Determine if the current index is valid.

```php
$valid = $queue->valid();
```


### Middleware

Custom middleware can be created by extending `\Fyre\Middleware\Middleware`, ensuring all below methods are implemented.

**Process**

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$handler` is a [*RequestHandler*](#request-handlers).

```php
$response = $middleware->process($request, $handler);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

The implemented method should call the `handle` method of the [*RequestHandler*](#request-handlers) to handle the next middleware in the queue.


## Middleware Registry

```php
use Fyre\Middleware\MiddlewareRegistry;
```

**Clear**

Clear all aliases and middleware.

```php
MiddlewareRegistry::clear();
```

**Map**

Map an alias to middleware.

- `$alias` is a string representing the middleware alias.
- `$middleware` is a string representing the [*Middleware*](#middleware) class name, or a closure that returns an instance of a [*Middleware*](middleware) class.

```php
MiddlewareRegistry::map($alias, $middleware);
```

**Use**

Load a shared Middleware instance.

- `$alias` is a string representing the middleware alias.

```php
$middleware = MiddlewareRegistry::use($alias);
```


## Request Handlers

- `$queue` is a [*MiddlewareQueue*](#middleware-queues).

```php
$handler = new RequestHandler($queue);
```

**Handle**

Handle the next middleware in the queue.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).

```php
$response = $handler->handle($request);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).