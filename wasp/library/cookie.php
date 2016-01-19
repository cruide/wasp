<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

class CookieException extends \Wasp\Exception {}
class Cookie extends \Wasp\stdObject
{
    private static $_instance, $_saved, $_need_save;
// -------------------------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        
        if( self::$_saved === null ) self::$_saved = false;
        if( self::$_need_save === null ) self::$_need_save = false;

        $tmp = \Wasp\Input::mySelf()->cookie();

        if( array_count($tmp) > 0 ) {
            if( array_key_isset('PHPSESSID', $tmp)  ) {
                unset($tmp['PHPSESSID']);
            }
            
            if( array_count($tmp) > 0 ) {
                foreach($tmp as $key=>$val) {
                    if( is_varible_name($key) ) {
                        if( $val != '' ) {
                            $this->_properties[ $key ] = unserialize(
                                @gzuncompress( base64_decode($val) )
                            );
                        } else {
                            $this->_properties[ $key ] = '';
                        }
                    }
                }
            }
        }
        
        unset($tmp);
        
        class_alias('\\Wasp\\Cookie', '\\Cookie');
    }
// -------------------------------------------------------------------------------------
    public function get($name = null)
    {
        if( $name === null ) {
            return $this->_properties;
        }
        
        return $this->__get( $name );
    }
// -------------------------------------------------------------------------------------
    public function set($name, $value = null)
    {
        if( is_cookie_varible_name($name) && isset($value) ) {
            parent::__set($name, $value);
            self::$_need_save = true;
            
        } else if( $this->__isset($name) && $value === null ) {
            $this->__unset($name);
            setcookie($name, '', time() - 3600, '/');
            
        } else {
            throw new CookieException(
                "Can not set a cookie `{$name}` with a value of `{$value}`"
            );
        }
        
        return $this;
    }
// -------------------------------------------------------------------------------------
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }
// -------------------------------------------------------------------------------------
    public function save()
    {
        if( empty($this->_properties) || !self::$_need_save ) {
            return false;
        }
        
        foreach($this->_properties as $key=>$val) {
            setcookie($key, base64_encode( gzcompress(serialize($val), 9) ), time() + 86400, '/');
        }
            
        self::$_saved = true;
    }
// -------------------------------------------------------------------------------------
    public function __destruct()
    {
        if( !headers_sent() && !self::$_saved ) {
            $this->save();
        }
        
        parent::__destruct();
    }
// -------------------------------------------------------------------------------------
    public static function isSaved()
    {
        return self::$_saved;
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
