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
            'redirect'  => null,
            'flash'     => []
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
        return $this;
    }
    
    public function data($key, $value) {
        $this->responseArray['data'][$key] = $value;
        return $this;
    }
    
    public function redirect($url) {
        $this->responseArray['redirect'] = $url;
        return $this;
    }

    public function flash($key, $value) {
        $this->responseArray['flash'][$key] = $value;
        return $this;
    }

    public function getResponseArray() {
        return $this->responseArray;
    }

}
