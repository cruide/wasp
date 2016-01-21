<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

  class NativeException extends Exception {}  

  class Native extends stdObject
  {
      public static $correctXHTML = false;
      private static $_globals;
      protected $_file, $_template_dir;
      
      const XHTML_CRRECTION_ON  = true;
      const XHTML_CRRECTION_OFF = false;
      const EXTENSION           = 'phtml';
      
// -------------------------------------------------------------------------------------
      public function __construct($tplDirectory)
      {
          parent::__construct();
          $this->setTemplateDir($tplDirectory);
      }
// -------------------------------------------------------------------------------------
      public function setTemplateDir($tplDir)
      {
		  if( !empty($tplDir) && is_dir($tplDir) ) {
			  $this->_template_dir = path_correct( $tplDir, true );
		  } else {
			  throw new NativeException("Incorrect template `{$tplDir}` directory");
		  }
      }
// -------------------------------------------------------------------------------------
      public static function assignGlobal($name, $value = '')
      {
          if( !is_array(self::$_globals) ) {
              self::$_globals = [];
          }
          
          if( !is_numeric($name) && is_string($name) && !is_array($name)) {
              self::$_globals[ $name ] = $value;
          } else if( !is_numeric($name) && is_string($name) && $value === null ) {
              unset( self::$_globals[$name] );
          } else if( is_array($name) && $value === '') {
              foreach($name as $key=>$val) {
                  if( !is_numeric($key) ) {
                      self::$_globals[ $key ] = $val;
                  }
              }
          }
      }
// -------------------------------------------------------------------------------------
      public function assign($name, $value = '')
      {
          if( !is_numeric($name) && is_string($name) && !is_array($name)) {
              $this->_properties[ $name ] = $value;
          } else if( !is_numeric($name) && is_string($name) && $value === null ) {
              $this->remove($name);
          } else if( is_array($name) && $value === '') {
              foreach($name as $key=>$val) {
                  if( !is_numeric($key) ) {
                      $this->_properties[ $key ] = $val;
                  }
              }
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function remove($name)
      {
          if( isset($this->_properties[ $name ]) ) {
              $this->_properties[ $name ] = null;
              unset($this->_properties[ $name ]);
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function fetch( $file_name, $need_ext = true, $xhtml = self::XHTML_CRRECTION_OFF )
      {
          if( $need_ext && !preg_match('#\.' . self::EXTENSION . '$#', $file_name) ) {
              $file_name = $file_name . '.' . self::EXTENSION;
          }
          
          $file_path = path_correct( $this->_template_dir . $file_name );

          if( !is_file($file_path) ) {
              throw new NativeException("Unknown template file {$file_path}");
          }

          if( $xhtml === self::XHTML_CRRECTION_ON ) {
              $_ = $this->_validXHTML( $this->_exec( $file_path ) );
          } else {
          	  $_ = $this->_exec( $file_path );
		  }
          
          return $_;
      }
// -------------------------------------------------------------------------------------
      public function display( $file_name, $xhtml = self::XHTML_CRRECTION_OFF )
      {
          echo $this->fetch( $file_name, true, $xhtml );
      }
// -------------------------------------------------------------------------------------
      public function clear()
      {
          $this->_properties = [];
      }
// -------------------------------------------------------------------------------------
      public function parseText( $str )
      {
          if( empty($str) ) return $str;
          
          foreach($this->_properties as $key=>$val) {
              if( is_scalar($val) ) {
                  $str = str_replace( '[$' . $key . ']', $val, $str );
              }
          }
          
          foreach(self::$_globals as $key=>$val) {
              if( is_scalar($val) ) {
                  $str = str_replace( '[$' . $key . ']', $val, $str );
              }
          }
          
          unset($plugins, $key, $val);
          
          return $str;
      } 
// -------------------------------------------------------------------------------------
      protected function _exec( $_native_execute_prepared_file ) 
      {
          if( !is_file($_native_execute_prepared_file) ) {
              throw new NativeException(
                  "Prepared file {$_native_execute_prepared_file} not found"
              );
          }

          if( is_array(self::$_globals) ) { 
          	  extract( self::$_globals ); 
          }
          
          if( is_array($this->_properties) ) { 
          	  extract( $this->_properties ); 
          }

          ob_start();
          ob_implicit_flush(true);

		  include($_native_execute_prepared_file);

          return ob_get_clean();
      }
// -------------------------------------------------------------------------------------
      protected function _validXHTML($text)
      {
          if( !empty($text) ) {
              $ret = preg_replace("#\s*(cellspacing|cellpadding|border|width|height|colspan|rowspan)\s*=\s*(\d+)\s*((\%|px)?)(\s*)#si", " $1=\"$2$3\" ", $text);
              $ret = preg_replace("#\s*(align|valign)\s*=\s*(\w+)\s*#si", " $1=\"$2\"", $ret);
              $ret = preg_replace("#<(img|input|meta|link|base)\s*(.*?)\s*/?>#is", "<$1 $2 />", $ret);
              $ret = preg_replace("#<br\s*/?>#is", "<br />", $ret);
              $ret = preg_replace("#<hr(.*?)\s*/?>#is", "<hr$1 />", $ret);
              $ret = preg_replace("#\s+>#is", ">", $ret);
              $ret = preg_replace("#\s*=\s*#is", "=", $ret);
              return $ret;
          } else { 
              return '';
          }
      }
  }
