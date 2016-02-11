<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

abstract class Controller extends \stdClass
{
    protected $input;
    protected $router;
    protected $session;
    protected $cookie;
    protected $config;
    protected $ui;
    protected $layout;
// -------------------------------------------------------------------------------------
    abstract public function actionDefault();
// -------------------------------------------------------------------------------------
    public function __construct()
    {
        $this->config  = cfg('config');
        $this->session = session();
        $this->input   = input();
        $this->cookie  = cookie();
        $this->layout  = theme();
        $this->router  = router();
        $this->ui      = new Native( $this->layout->getThemePath() . DIR_SEP . 'views' );
    }
// -------------------------------------------------------------------------------------
    public function getUi()
    {
        return $this->ui;
    }
// -------------------------------------------------------------------------------------
    protected function json( $data )
    {
        $this->layout
             ->disable()
             ->setHeader( CONTENT_TYPE_JSON );
             
        return json_encode( $data );
    }
// -------------------------------------------------------------------------------------
    protected function javascript( $template )
    {
        $this->layout
             ->disable()
             ->setHeader( CONTENT_TYPE_JS );
             
        return $this->ui->fetch( $template );
    }
// -------------------------------------------------------------------------------------
    protected function ajax( $template )
    {
        $this->layout
             ->disable()
             ->setHeader( CONTENT_TYPE_HTML );
             
        return $this->ui->fetch( $template );
    }
// -------------------------------------------------------------------------------------
    public function getLayout()
    {
        return $this->layout;
    }
}