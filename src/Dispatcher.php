<?php
declare(strict_types=1);

namespace Jp\Middleware;

use Closure;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Dispatcher implements RequestHandlerInterface
{
    private ContainerInterface $container;
    private Closure $app;

    /** @var string[] $middlewares */
    private array $middlewares = [];

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param Closure $app
     */
    public function __construct(
        ContainerInterface $container,
        Closure $app
    ) {
        $this->container = $container;
        $this->app = $app;
    }

    /**
     * Add middlewares to the middleware stack.
     *
     * @param string[] $middlewares
     *
     * @return void
     */
    public function addMiddlewares(array $middlewares) : void
    {
        foreach ($middlewares as $middleware) {
            $this->addMiddleware($middleware);
        }
    }

    /**
     * Add a middleware to the middleware stack.
     *
     * @param string $middleware
     *
     * @return void
     */
    public function addMiddleware(string $middleware) : void
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * Dispatch the app through the middleware stack and return the response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        if (!$middleware = array_shift($this->middlewares)) {
            // At the center of the middleware stack, an application turns the
            // request into a response.
            $response = ($this->app)($request);

            return $response;
        }

        $middleware = $this->container->get($middleware);

        return $middleware->process($request, $this);
    }
}
