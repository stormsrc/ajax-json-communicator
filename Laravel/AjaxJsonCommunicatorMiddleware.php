<?php
namespace Storm\Communicator\Laravel;

use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;
use Storm\Communicator\ClientSide;

class AjaxJsonCommunicatorMiddleware implements Middleware {
    /**
     *
     * @var \Illuminate\Contracts\Routing\ResponseFactory
     */
    protected $responseFactory;
    
    function __construct(ResponseFactory $responseFactory) {
        $this->responseFactory = $responseFactory;
    }

    public function handle($request, \Closure $next) {
        // Get the response so we can swap it out for our version later
        $response = $next($request);
        // The request method should be POST and should be accepting and JSON
        // response.
        if ($request->getMethod() === 'POST' && $request->wantsJson()) {
            $data = ClientSide::get()->content($response->getContent())->getResponseArray();
            $status = $response->getStatusCode();
            $jsonResponse = $this->responseFactory->json($data, $status);
            return $jsonResponse;
        }
        
        return $response;
    }

}
