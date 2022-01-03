# FyreMiddleware

**FyreMiddleware** is a free, middleware library for *PHP*.


## Table Of Contents
- [Installation](#installation)
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
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
```


## Middleware Queues

```php
$queue = new MiddlewareQueue();
```

**Add**

Add *Middleware*.

- `$middleware` is a *Middleware*.

```php
$queue->add($middleware);
```

**Count**

Get the *Middleware* count.

```php
$count = $queue->count();
```

**Current**

Get the *Middleware* at the current index.

```php
$middleware = $queue->current();
```

**Insert At**

Insert *Middleware* at a specified index.

- `$index` is a number representing the index.
- `$middleware` is a *Middleware*.

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

Prepend *Middleware*.

- `$middleware` is a *Middleware*.

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

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/fyreserver).
- `$handler` is a *RequestHandler*.

```php
$response = $middleware->process($request, $handler);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/fyreserver).

The implemented method should call the `handle` method of the *RequestHandler* to handle the next middleware in the queue.


## Request Handlers

- `$queue` is a *MiddlewareQueue*.

```php
$handler = new RequestHandler($queue);
```

**Handle**

Handle the next middleware in the queue.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/fyreserver).

```php
$response = $handler->handler($request);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/fyreserver).