<?php
namespace Storm\Communicator\Laravel;

use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Support\Arrayable;
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
    
    protected function registerFlash() {
        if (Session::has('flash.new')) {
            $flashKeys = Session::pull('flash.new', []);
            foreach ($flashKeys AS $key) {
                if (!Session::has($key)) {
                    continue;
                }
                $value = Session::pull($key);
                if ($value instanceof Arrayable) {
                    $value = $value->toArray();
                }
                ClientSide::get()->flash($key, $value);
            }
        
        }
    }

    public function handle($request, \Closure $next) {
        // Get the response so we can swap it out for our version later
        $response = $next($request);
        // Register flash messages
        $this->registerFlash();
        // The request method should be POST and should be accepting and JSON
        // response.
        if ($request->getMethod() === 'POST' && $request->wantsJson()) {
            $status = $response->getStatusCode();
            // Redirects
            if ($status == 302) {
                $url = $response->getTargetUrl();
                ClientSide::get()->redirect($url);
                $status = 200;
            }
            $data = ClientSide::get()->content($response->getContent())->getResponseArray();
            $jsonResponse = $this->responseFactory->json($data, $status);
            return $jsonResponse;
        }
        
        return $response;
    }

}
