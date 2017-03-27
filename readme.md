This is a basic PSR-15 middleware dispatcher using PSR-11 ContainerInterface to retrieve middlewares.

## Usage

```php
$app = function (ServerRequestInterface $request) : ResponseInterface {
    // This closure will be executed at the center of the middleware stack.
    // Use it to wrap your application, or to return a default response.
}

$dispatcher = new Dispatcher($container, $app);

$dispatcher->addMiddlewares([
    ExampleMiddleware::class,
    // ...
]);

$response = $dispatcher->process($request);
```
