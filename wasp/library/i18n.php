<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

final class I18n extends \stdClass
{
    private static $_instance;
    
    private $strings;
    private $lagnuages = [
        'EN' => 'English',
        'RU' => 'Русский',
        'UA' => 'Український',
        'BE' => 'Беларускі',
        'DE' => 'Deutsch',
    ];
    
    private $DEFAULT_LANG = 'EN';
    private $CURRENT_LANG = 'EN';
// -----------------------------------------------------------------------------
    public function __construct()
    {
        /**
        * Просматриваем какие языки есть
        */
        foreach($this->lagnuages as $key=>$val) {
            $LF = wasp_strtolower( $key . '.php' );
            if( !is_file(LANGUAGE_DIR . DIR_SEP . $LF) ) {
                unset($this->lagnuages[ $key ]);
            }
        }
        
        unset($LF, $key, $val);
        
        /**
        * Смотрим язык установленный в конфиге
        */
        $config = cfg('config')->application;
        $DL     = (!empty($config->language)) ? $config->language : null;

        if( !empty($DL) && array_key_isset($DL, $this->lagnuages) ) {
            $this->DEFAULT_LANG = $this->CURRENT_LANG = wasp_strtoupper( $DL );
        }

        unset($config, $DL);

        $cookie_lang = cookie()->get('LANG');
        
        if( !empty($cookie_lang) && array_key_isset($cookie_lang, $this->lagnuages) ) {
            $this->CURRENT_LANG = $cookie_lang;
        }
        
        /**
        * Загружаем языковые пакеты
        */
        $this->strings     = new stdObject();
        $default_lang_file = wasp_strtolower( $this->DEFAULT_LANG . '.php' );

        if( is_dir(LANGUAGE_DIR) && is_file(LANGUAGE_DIR . DIR_SEP . $default_lang_file) ) {
            $default_lng_strings = include( LANGUAGE_DIR . DIR_SEP . $default_lang_file ); 
            
            if( array_count($default_lng_strings) > 0 ) {
                foreach($default_lng_strings as $key=>$val) {
                    if( is_varible_name($key) && is_scalar($val) ) {
                        $this->strings->$key = $val;
                    }
                }
            }
        }
        
        if( $this->DEFAULT_LANG != $this->CURRENT_LANG ) {
            $default_lang_file  = wasp_strtolower( $this->CURRENT_LANG . '.php' );
            
            if( is_dir(LANGUAGE_DIR) && is_file(LANGUAGE_DIR . DIR_SEP . $default_lang_file) ) {
                $default_lng_strings = include( LANGUAGE_DIR . DIR_SEP . $default_lang_file ); 
                
                if( array_count($default_lng_strings) > 0 ) {
                    foreach($default_lng_strings as $key=>$val) {
                        if( is_varible_name($key) && is_scalar($val) ) {
                            $this->strings->$key = $val;
                        }
                    }
                }
            }
        }
        
        $this->getControllerLang( 
            router()->getControllerName() 
        );
    }
// -----------------------------------------------------------------------------
    public function getControllerLang( $controller_name )
    {
        if( is_controller_exists($controller_name) ) {
            $lng_dir          = LANGUAGE_DIR . DIR_SEP . strtolower($controller_name);
            $current_lng_file = wasp_strtolower( $this->CURRENT_LANG . '.php' );
            $default_lng_file = wasp_strtolower( $this->DEFAULT_LANG . '.php' );
            
            if( is_dir($lng_dir) && is_file($lng_dir . DIR_SEP . $default_lng_file) ) {
                $strings = include( $lng_dir . DIR_SEP . $default_lng_file );
                
                if( array_count($strings) > 0 ) {
                    foreach($strings as $key=>$val) {
                        if( is_varible_name($key) && is_scalar($val) ) {
                            $this->strings->$key = $val;
                        }
                    }
                }
            }

            if( $this->DEFAULT_LANG != $this->CURRENT_LANG ) {
                if( is_dir($lng_dir) && is_file($lng_dir . DIR_SEP . $current_lng_file) ) {
                    $strings = include( $lng_dir . DIR_SEP . $current_lng_file );
                    
                    if( array_count($strings) > 0 ) {
                        foreach($strings as $key=>$val) {
                            if( is_varible_name($key) && is_scalar($val) ) {
                                $this->strings->$key = $val;
                            }
                        }
                    }
                }
            }
        }
    }
// -----------------------------------------------------------------------------
    /**
    * Получение кода языка, 
    * установленного по умолчанию
    */
    public function getDefaultLang()
    {
        return $this->DEFAULT_LANG;
    }
// -----------------------------------------------------------------------------
    /**
    * Получение текущего языка
    */
    public function getCurrentLang()
    {
        return $this->CURRENT_LANG;
    }
// -----------------------------------------------------------------------------
    /**
    * Установка текущего языка
    */
    public function setCurrentLang( $lang_id )
    {
        $lang_id = strtoupper($lang_id);
        
        if( array_key_isset($lang_id, $this->lagnuages) ) {
            Cookie::mySelf()->set('LANG', $lang_id);
        }
        
        return $this;
    }
// -----------------------------------------------------------------------------
    /**
    * Получение списка поддерживаемых языков
    */
    public function getLanguages()
    {
        return $this->lagnuages;
    }
// -----------------------------------------------------------------------------
    public function has( $key )
    {
        return ( !empty($key) && isset($this->strings->$key) );
    }
// -----------------------------------------------------------------------------
    public function get( $key, array $values = [] )
    {
        if( $this->has($key) ) {
            $_ = $this->strings->$key;
            
            if( array_count($values) > 0 ) {
                foreach($values as $key=>$val) {
                    if( is_scalar($val) ) {
                        $_ = str_replace(":{$key}", $val, $_);
                    }
                }
            }
            
            return $_;
        }
        
        return "::{$key}::";
    }
// -----------------------------------------------------------------------------
    public static function mySelf()
    {
        if( null === self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}

