<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

abstract class Service extends \stdClass
{
    protected $config, $session, $router, $input, $cookie;
// ---------------------------------------------------------------------------------
    public function __construct()
    {
        $this->config  = cfg('config');
        $this->cookie  = Cookie::mySelf();
        $this->router  = Router::mySelf();
        $this->input   = Input::mySelf();
        $this->session = Session::mySelf();

        if( method_exists($this, '_prepare') ) {
            $this->_prepare();
        } 
    }
// ---------------------------------------------------------------------------------
    abstract public function execute();
// ---------------------------------------------------------------------------------
    public function __destruct()
    {
        unset($this->config, $this->cookie, $this->session, $this->input, $this->router, $this->db);
        memory_clear();
    }
}
