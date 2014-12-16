<?php
namespace Storm\Communicator\Laravel;

use Storm\Communicator\ClientSide;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Contracts\Routing\ResponseFactory;

class AjaxJsonCommunicatorProvider extends ServiceProvider {
    
    public function register() {
        
    }
    
    public function boot(Registrar $router, ResponseFactory $responseFactory) {
        
        $router->get('/resources/js/ajaxcommunicator.js', function () use ($responseFactory) {
            $content = ClientSide::get()->getJavascript();
            $response = $responseFactory->make($content, 200, [ 'Content-Type' => 'text/javascript' ]);
            return $response;
        });
    }

}
