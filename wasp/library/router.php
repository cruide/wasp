<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

final class RouterException extends \Wasp\Exception { }

final class Router extends \stdClass
{
    private $controller;
    private $method;
    private $action;
    private $request;
    private $uri;
    
    private static $_instance;
    private static $regexps;
// -------------------------------------------------------------------------------------
    public function __construct()
    {
        global $_REQUEST_URI;

        $superfluous = ['.html', '.htm', '.php5', '.php', '.php3', '.shtml', '.phtml', '.dhtml', '.xhtml', '.inc', '.cgi', '.pl','.xml', '.js'];
        $this->uri   = preg_replace( "#/+#s", '/', $_REQUEST_URI );
        $this->uri   = preg_replace( ["#/$#s", "#^/+#"], '', $this->uri );
        $this->uri   = $this->request = str_replace($superfluous, '', $this->uri);

        $this->_load_routes();
        
        if( array_count(self::$regexps) > 0 ) {
            foreach(self::$regexps as $key=>$val) {
                if( preg_match('#' . $val['regexp'] . '#is', $this->uri) ) {
                    $this->uri = preg_replace('#' . $val['regexp'] . '#is', $val['replace'], $this->uri);
                    break;
                }
            }
        }

        if( preg_match("%[^a-z0-9\/\-\.]%isu", $this->uri) ) {
            wasp_error("Abnormal request: {$this->uri}", 500);
        }

        class_alias('\\Wasp\\Router', '\\Router');
       
        if( empty($this->uri) ) {
            $this->controller = 'Index';
            $this->method     = 'Default';
            $this->action     = snake_case($this->controller) . '/' . snake_case($this->method);
            
            return;
        }
        
        $do         = explode('/', $this->uri);
        $controller = ( isset($do[0]) ) ? $do[0] : null;
        $method     = ( isset($do[1]) ) ? $do[1] : null;
        
        if( empty($controller) || !is_action_name($controller) ) {
            $this->controller = 'Index';
            $this->method     = 'Default';
            
        } else {
            $this->controller = studly_case($controller);
            
            if( empty($method) || !is_action_name($method) ) {
                $this->method = 'Default';
            } else {
                $this->method = studly_case($method);
            }
        }
        
        $this->action = snake_case($this->controller, '-') . '/' . snake_case($this->method, '-');

        if( $this->controller == 'Coreinfo' ) {
            if( $this->method == 'Json' ) {
                $_              = new \stdClass();
                $_->name        = CORE_NAME;
                $_->description = FRAMEWORK;
                $_->version     = CORE_VERSION;
                $_->status      = CORE_VERSION_NAME;
                $_->author      = 'Tishchenko Alexander';
                
                if( !headers_sent() ) {
                    header('Cache-Control: no-cache, must-revalidate');
                    header('Expires: ' . date('r', time() - 86400));
                    header( CONTENT_TYPE_JSON );
                }
                
                exit( json_encode($_) );
            } else {
                if( !headers_sent() ) {
                    header( CONTENT_TYPE_XML );
                }
                
                exit( 
                    "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<coreinfo>\n\t<name><![CDATA[" . CORE_NAME . 
                    "]]></name>\n\t<description><![CDATA[" . FRAMEWORK . "]]></description>\n\t" . 
                    "<version><![CDATA[" . CORE_VERSION . "]]></version>\n" . 
                    "\t<status><![CDATA[" . CORE_VERSION_NAME . "]]></status>\n" .
                    "\t<author><![CDATA[Tishchenko Alexander]]></author>\n" .
                    "</coreinfo>" 
                );
            }
        } else if( $this->controller == 'Phpinfo' && DEVELOP_MODE ) {
            phpinfo();
            exit();
        } else {
            if( !is_controller_exists($this->controller) ) {
                show_error_404();
            }
        }
        
        array_shift($do);
        array_shift($do);

        $this->_set_get_params($do);
    }
// -------------------------------------------------------------------------------------
    public function getAction()
    {
        return $this->action;
    }
// -------------------------------------------------------------------------------------
    public function getControllerName()
    {
        return $this->controller;
    }
// -------------------------------------------------------------------------------------
    public function getMethodName()
    {
        return $this->method;
    }
// -------------------------------------------------------------------------------------
    private function _set_get_params($_array = [])
    {
        global $_GET;

        if( array_count($_array) > 0 && is_even(count($_array)) ) {
            $_array_count = floor( count($_array) / 2 );

            for($i=0; $i/2 < $_array_count; $i+=2) {
                $_GET[ $_array[$i] ] = ( !empty($_array[$i+1]) ) ? $_array[$i+1] : '';
            }
        }
    }
// -------------------------------------------------------------------------------------
    public static function route($regexp, $replace)
    {
        if( self::$regexps === null ) {
            self::$regexps = [];
        }

        if( !empty($replace) && !empty($regexp) ) {
            self::$regexps[] = [
                'regexp'   => (string)$regexp,
                'replace' => (string)$replace,
            ];
        }
    }
// -------------------------------------------------------------------------------------
    protected function _load_routes()
    {
        if( is_file(APP_DIR . DIR_SEP . 'routes.php') ) {
            $routes = include(APP_DIR . DIR_SEP . 'routes.php');
            
            if( is_array($routes) && count($routes) > 0 ) {
                foreach($routes as $route) {
                    if( isset($route['regexp']) && isset($route['replace']) ) {
                        self::$regexps[] = [
                            'regexp'   => (string)$route['regexp'],
                            'replace' => (string)$route['replace'],
                        ];
                    }
                }
            }
        }
    } 
// -------------------------------------------------------------------------------------
    public static function mySelf()
    {
        if( null === self::$_instance ) {
            self::$_instance = new self();
        }
 
        return self::$_instance;
    }
}