<?php if( !defined('__ALIASES__') ) { define('__ALIASES__', true);
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/
  
    function session()
    {
        return \Wasp\Session::mySelf();
    }

    function input()
    {
        return \Wasp\Input::mySelf();
    }

    function cookie()
    {
        return \Wasp\Cookie::mySelf();
    }

    function router()
    {
        return \Wasp\Router::mySelf();
    }

    function i18n()
    {
        return \Wasp\I18n::mySelf();
    }

    function theme()
    {
        return \Wasp\Theme::mySelf();
    }
    
    function app()
    {
        return \Wasp\App::mySelf();
    }
}