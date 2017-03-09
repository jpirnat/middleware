<?php
declare(strict_types=1);

namespace Jp\Middleware;

use Interop\Http\Factory\ResponseFactoryInterface;
use Interop\Http\ServerMiddleware\DelegateInterface;
use Interop\Http\ServerMiddleware\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ExampleMiddleware implements MiddlewareInterface
{
    /** @var ResponseFactoryInterface $responseFactory */
    protected $responseFactory;

    /**
     * Constructor.
     *
     * @param ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Process an incoming server request and return a response, optionally
     * delegating to the next middleware component to create the response.
     *
     * @param ServerRequestInterface $request
     * @param DelegateInterface $delegate
     *
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        DelegateInterface $delegate
    ) : ResponseInterface {
        // Optionally modify the request.
        $request = $request->withAttribute('foo', 'bar');

        // Create the response in this middleware, or delegate the response's
        // creation to the rest of the middleware stack.
        $response = $this->responseFactory->createResponse(200);
        // OR:
        // $response = $delegate->process($request);

        // Optionally modify the response.
        $response = $response->withHeader('foo', 'bar');

        return $response;
    }
}
