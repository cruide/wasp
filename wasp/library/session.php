<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

  class Session extends stdObject
  {       
      protected $sessionId;
      private static $_instance;
// -------------------------------------------------------------------------------------
      public function __construct()
      {
          global $_SESSION;

          if( session_start() ) {
              $this->sessionId = session_id();
          }
          
          parent::__construct($_SESSION);
      }
// -------------------------------------------------------------------------------------
      public function __set($name, $value)
      {
          global $_SESSION;
          
          if( is_string($name) && is_varible_name($name) ) {
              $_SESSION[ $name ] = $this->_properties[ $name ] = $value;
          }
      }
// -------------------------------------------------------------------------------------
      public function id( $hash = false )
      {
          return ($hash) ? md5($this->sessionId) : $this->sessionId;
      }
// -------------------------------------------------------------------------------------
      public function destroy()
      {
          session_destroy();
      }
// -------------------------------------------------------------------------------------
      public static function mySelf()
      {
          if( null === self::$_instance ) {
              self::$_instance = new self();
          }
 
          return self::$_instance;
      }
// -------------------------------------------------------------------------------------
  }
