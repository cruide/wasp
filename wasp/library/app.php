<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

final class AppException extends Exception { }

final class App extends \stdClass
{
    private static $controller;
    private static $_instance;
// -------------------------------------------------------------------------------------
    public function __construct()
    {
        if( is_file(APP_DIR . DIR_SEP . 'bootstrap.php') ) {
            require(APP_DIR . DIR_SEP . 'bootstrap.php');
        }
        
        $router     = router();
        $input      = input();
        $controller = $router->getControllerName();

        I18n::mySelf();
        
        if( is_controller_exists($controller) ) {
            $className = '\\App\\Controllers\\' . $controller;

            if( class_exists($className) && is_subclass_of($className, '\\Wasp\\Controller') ) {
                try {
                    self::$controller = new $className();
                } catch( AppException $e ) {
                    wasp_error( $e->getMessage(), $e->getFile(), $e->getLine() );
                }
            }
        } else {
            show_error_404();
        }
    }
// -------------------------------------------------------------------------------------
    public function execute()
    {
        global $events, $_SERVER;

        $this->_execute_services();
        
        $method = router()->getMethodName();
        $type   = strtolower( $_SERVER['REQUEST_METHOD'] );
        
        if( method_exists(self::$controller, $type . $method) ) {
            $method = $type . $method;
        } else if( method_exists(self::$controller, 'any' . $method) ) {
            $method = 'any' . $method;
        } else {
            show_error_404();
        }

        $this->_prepare();
        
        try {
          
            if( method_exists(self::$controller, '_before') ) {
                self::$controller->_before();
            }

            $params = input()->get();
           
            if( !empty($params) ) {
                $content = call_user_func_array([self::$controller, $method], $params);
            } else {
                $content = call_user_func([self::$controller, $method]);
            }

            if( method_exists(self::$controller, '_after') ) {
                self::$controller->_after();
            }
          
            if( is_ajax() ) {
                theme()->disable();
            }

            theme()->display( $content );
          
        } catch( AppException $e ) {
            wasp_error( $e->getMessage() );
        }
        
        \Wasp\Log::mySelf()->write();
    }
// -------------------------------------------------------------------------------------
    private function _prepare()
    {
        $theme_url = theme()->getThemeUrl();
        
        $vars = [
            'base_url'          => BASE_URL,
            'content_url'       => CONTENT_URL,
            'core_name'         => CORE_NAME,
            'core_version'      => CORE_VERSION,
            'core_version_name' => CORE_VERSION_NAME,
            'framework'         => FRAMEWORK,
            'timer_varible'     => time(),
            
            'theme_url'         => $theme_url,
            'css_url'           => $theme_url . '/css',
            'js_url'            => $theme_url . '/js',
            'images_url'        => $theme_url . '/images',
            
            'action'            => router()->getAction(),
            'controller_name'   => router()->getControllerName(),
            'method_name'       => router()->getMethodName(),
        ];
        
        $config = cfg('config')->application;
        if( !empty($config->url_suffix) ) {
            $vars['url_suffix'] = (string)$config->url_suffix;
        } else {
            $vars['url_suffix'] = '.html';
        }
        
        $msg = unserialize( pickup_temp('redirect') );
        
        if( !empty($msg['message']) ) {
            $vars['redirect_message'] = $msg['message'];
        }
        
        if( !empty($msg['error']) ) {
            $vars['redirect_error'] = $msg['error'];
        }

        $smarty = new \Smarty();

        foreach($vars as $key=>$val) {
            $smarty->assignGlobal($key, $val);
        }
        
        unset($theme, $config, $msg, $smarty, $key, $val);
    }
    
// -------------------------------------------------------------------------------------
    private function _execute_services()
    {
        if( is_dir(SERVICES_DIR) ) {
            $services = get_files_list( SERVICES_DIR, ['php'] );

            if( array_count($services) > 0 ) {
                ksort( $services );

                foreach($services as $key=>$val) {
                    $service_name  = str_replace( '.php', '', $key );
                    $service_class = '\\App\\Services\\' . studly_case( $service_name );

                    try {
                        $tmp = new $service_class();
                    } catch( AppException $e ) {
                        wasp_error( $e->getMessage() );
                    }
                    
                    if( $tmp instanceof Service ) {
                        $tmp->execute();
                    } else {
                        wasp_error(
                            'Incorrect instance of service class ' . $tmp->className()
                        );
                    }
                    
                    unset($tmp);
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