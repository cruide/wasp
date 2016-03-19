<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

abstract class Library extends \stdClass
{
    protected $config, $session, $router, $input, $cookie;
// ---------------------------------------------------------------------------------
    public function __construct()
    {
        $this->config  = cfg('config');
        $this->cookie  = cookie();
        $this->router  = router();
        $this->input   = input();
        $this->session = session();

        if( method_exists($this, '_prepare') ) {
            $this->_prepare();
        } 
    }
// ---------------------------------------------------------------------------------
    public function __destruct()
    {
        unset($this->config, $this->cookie, $this->session, $this->input, $this->router);
        memory_clear();
    }
}
