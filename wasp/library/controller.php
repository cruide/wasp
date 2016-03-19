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
    abstract public function anyDefault();
// -------------------------------------------------------------------------------------
    public function __construct()
    {
        $this->config  = cfg('config');
        $this->session = session();
        $this->input   = input();
        $this->cookie  = cookie();
        $this->layout  = theme();
        $this->router  = router();
        $this->ui      = new Ui();

        $this->ui->enableSecurity('Wasp_Smarty_Security');
        $this->ui->setTemplateDir( $this->layout->getThemePath() . DIR_SEP . 'views' . DIR_SEP );
        
        $temp_dir = TEMP_DIR . DIR_SEP . 'smarty' . DIR_SEP . theme()->getThemeName() . DIR_SEP . 'views';

        if( !is_dir($temp_dir) ) {
            wasp_mkdir( $temp_dir );
        }
        
        $this->ui->setCompileDir( $temp_dir . DIR_SEP );
//        $this->ui->setCacheDir('');
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