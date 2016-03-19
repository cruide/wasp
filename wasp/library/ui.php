<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

class UiException extends \Wasp\Exception { }

final class Ui
{
    protected $smarty;
// -------------------------------------------------------------------------------------
    public function __construct()
    {
        $this->smarty = new \Smarty();
    }
// -------------------------------------------------------------------------------------
    public function fetch($template = null, array $values = [])
    {
        if( !preg_match('#\.tpl$#', $template) ) {
            $template = $template . '.tpl';
        }
        
        foreach($values as $key=>$val) {
            $this->smarty->assign($key, $val);
        }
          
        return $this->smarty->fetch($template);
    }
// -------------------------------------------------------------------------------------
    public function __call($method, $params = null)
    {
        $_ = null;
        
        try {
            if( !empty($params) ) {
                $_ = call_user_func_array([$this->smarty, $method], $params);
            } else {
                $_ = call_user_func([$this->smarty, $method]);
            }
        } catch( UiException $e ) {
            wasp_error( $e->getMessage() );
        }
        
        return $_;
    }
}