<?php
declare(strict_types=1);

namespace Jp\Middleware;

use Interop\Http\Factory\ResponseFactoryInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class MiddlewareDispatcher implements DelegateInterface
{
    /** @var string[] $middlewares */
    protected $middlewares = [];

    /** @var ResponseFactoryInterface $responseFactory */
    protected $responseFactory;

    /** @var ContainerInterface $container */
    protected $container;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param ContainerInterface $container
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        ContainerInterface $container
    ) {

        $this->responseFactory = $responseFactory;
        $this->container = $container;
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
            // Default response.
            $response = $this->responseFactory->createResponse(200);

            return $response;
        }

        $middleware = $this->container->get($middleware);

        return $middleware->process($request, $this);
    }
}
