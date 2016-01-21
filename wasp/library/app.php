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
        
        $router     = Router::mySelf();
        $input      = Input::mySelf();
        $controller = $router->getControllerName();

        I18n::mySelf();
        
        if( is_controller_exists($controller) ) {
            $className = '\\App\\Controllers\\' . $controller;

            if( class_exists($className) && is_subclass_of($className, '\\Wasp\\Controller') ) {
                try {
                    self::$controller = new $className();
                } catch( KernelException $e ) {
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
        $this->_execute_hooks();
        
        $method = Router::mySelf()->getMethodName();

        if( Input::mySelf()->isGet() && method_exists(self::$controller, 'get' . $method) ) {
            $method = 'get' . $method;
        } else if( Input::mySelf()->isPost() && method_exists(self::$controller, 'post' . $method) ) {
            $method = 'post' . $method;
        } else if( method_exists(self::$controller, 'action' . $method) ) {
            $method = 'action' . $method;
        } else {
            show_error_404();
        }

        try {
          
            if( method_exists(self::$controller, '_before') ) {
                self::$controller->_before();
            }

            $this->_prepare();
            
            $params = Input::mySelf()->get();
            
            if( !empty($params) ) {
                $content = call_user_func_array([self::$controller, $method], $params);
            } else {
                $content = call_user_func([self::$controller, $method]);
            }

            if( method_exists(self::$controller, '_after') ) {
                self::$controller->_after();
            }
          
            if( is_ajax() ) {
                self::$controller->getLayout()->disable();
            }

            self::$controller->getLayout()->display( $content );
          
        } catch( KernelException $e ) {
            wasp_error( $e->getMessage() );
        }
        
    }
// -------------------------------------------------------------------------------------
    private function _prepare()
    {
        $theme_url = Theme::mySelf()->getThemeUrl();
        
        Native::assignGlobal('base_url'       , BASE_URL);
        Native::assignGlobal('content_url'    , CONTENT_URL);
        Native::assignGlobal('core_name'      , CORE_NAME);
        Native::assignGlobal('core_version'   , CORE_VERSION);
        Native::assignGlobal('core_ver_name'  , CORE_VERSION_NAME);
        Native::assignGlobal('framework'      , FRAMEWORK);
        Native::assignGlobal('timer_varible'  , time());

        Native::assignGlobal('theme_url'      , $theme_url);
        Native::assignGlobal('css_url'        , $theme_url . '/css');
        Native::assignGlobal('js_url'         , $theme_url . '/js');
        Native::assignGlobal('images_url'     , $theme_url . '/images');

        Native::assignGlobal('do_request'     , Router::mySelf()->getAction());
        Native::assignGlobal('controller_name', Router::mySelf()->getControllerName());
        Native::assignGlobal('method_name'    , Router::mySelf()->getMethodName());
        
        $config = cfg('config')->application;
        if( !empty($config->url_suffix) ) {
            Native::assignGlobal('url_suffix', (string)$config->url_suffix);
        } else {
            Native::assignGlobal('url_suffix', '.html');
        }
        
        $msg = unserialize( pick_temp('redirect') );
        
        if( !empty($msg['message']) ) {
            Native::assignGlobal('redirect_message', $msg['message']);    
        }
        
        if( !empty($msg['error']) ) {
            Native::assignGlobal('redirect_error', $msg['error']);    
        }
                  
        unset($theme, $config, $msg);
    }
    
// -------------------------------------------------------------------------------------
    private function _execute_hooks()
    {
        if( is_dir(HOOKS_DIR) ) {
            $hooks = get_files_list( HOOKS_DIR, ['php'] );

            if( array_count($hooks) > 0 ) {
                ksort( $hooks );

                foreach($hooks as $key=>$val) {
                    $hook_name  = str_replace( '.php', '', $key );
                    $hook_class = '\\App\\Hooks\\' . studly_case( $hook_name );

                    try {
                        $tmp = new $hook_class();
                    } catch( KernelException $e ) {
                        wasp_error( $e->getMessage() );
                    }
                    
                    if( $tmp instanceof Hook ) {
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