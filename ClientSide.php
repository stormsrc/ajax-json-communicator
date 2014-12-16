<?php
namespace Storm\Communicator;

class ClientSide {
    /**
     *
     * @var \Storm\Communicator\ClientSide
     */
    protected static $instance = null;
    /**
     *
     * @var array
     */
    protected $responseArray;
    
    /**
     * 
     * @return \Storm\Communicator\ClientSide
     */
    public static function get() {
        if (is_null(static::$instance)) {
            static::$instance = new ClientSide;
        }
        
        return static::$instance;
    }
    
    function __construct() {
        $this->responseArray = [
            'content'   => '',
            'data'      => [],
            'redirect'  => null
        ];
    }
    
    /**
     * 
     * @return string
     */
    public function getJavascript() {
        return <<<EOT
/**
 * Storm Ajax Json Communicator
 * Client-side code
 */
var Comm_ServerSide = { busy: false, currentURL: null };
Comm_ServerSide.form = function (form, url, data, callback) {};
Comm_ServerSide.load = function (url, callback) {};
Comm_ServerSide.hook = function (event, callback) {};
EOT;
    }
    
    public function content($content) {
        $this->responseArray['content'] = $content;
    }
    
    public function data($key, $value) {
        $this->responseArray['data'][$key] = $value;
    }
    
    public function redirect($url) {
        $this->responseArray['redirect'] = $url;
    }


    public function getResponseArray() {
        return $this->responseArray;
    }

}
