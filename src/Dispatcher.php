<?php
declare(strict_types=1);

namespace Jp\Middleware;

use Closure;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Dispatcher implements DelegateInterface
{
    /** @var string[] $middlewares */
    protected $middlewares = [];

    /** @var ContainerInterface $container */
    protected $container;

    /** @var Closure $app */
    protected $app;

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
     * Dispatch the next available middleware and return the response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request) : ResponseInterface
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
