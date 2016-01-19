<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

  final class Input extends \stdClass
  {
      private static $_instance;
      protected $properties;
      protected $server;
      protected static $handlers;
// -------------------------------------------------------------------------------------
      public function __construct()
      {
          global $_GET, $_POST, $_COOKIE, $_FILES, $_SERVER;

          $this->properties = [];
          $this->server     = $_SERVER;
          
          if( self::$handlers == null ) self::$handlers = [];
          
          if( isset($_POST) && array_count($_POST) > 0 ) {
              foreach($_POST as $key=>$val) {
                  $this->properties['POST'][ $this->_clean_key($key) ] = $this->_clean_val($val);
              }
          }

          if( isset($_GET) && array_count($_GET) > 0 ) {
              foreach($_GET as $key=>$val) {
                  $this->properties['GET'][ $this->_clean_key($key) ] = $this->_clean_val($val);
              }
          }
          
          if( isset($_COOKIE) && array_count($_COOKIE) > 0 ) {
              foreach($_COOKIE as $key=>$val) {
                  $this->properties['COOKIE'][ $this->_clean_key($key) ] = $this->_clean_val($val);
              }
          }
          
          if( isset($_FILES) && array_count($_FILES) > 0 ) {
              foreach($_FILES as $key=>$val) {
                  $this->properties['FILES'][ $this->_clean_key($key) ] = $this->_clean_val($val);
              }
          }
          
          class_alias('\\Wasp\\Input', '\\Input');
      }
// -------------------------------------------------------------------------------------
      public function cookie($name = null, $xss = false)
      {
          if( !array_key_isset('COOKIE', $this->properties) ) {
              return null;
          }

          if( $name !== null && array_key_isset($name, $this->properties['COOKIE']) ) {
              $value = ($xss === true) ? $this->_xss_clean($this->properties['COOKIE'][ $name ])
                                       : $this->properties['COOKIE'][ $name ];
              
              if( isset(self::$handlers['COOKIE'][ $name ]) ) {
                  $closure = self::$handlers['COOKIE'][ $name ];

                  return $closure( $value );
              }
              
              return $value;
              
          } else if( $name == null ) {
              if( empty($this->properties['COOKIE']) ) {
                  return null;
              }
              
              $_ = [];
              
              foreach($this->properties['COOKIE'] as $key=>$val) {
                  if( isset(self::$handlers['COOKIE'][ $key ]) ) {
                      $closure   = self::$handlers['COOKIE'][ $key ];
                      $_[ $key ] = $closure( ($xss === true) ? $this->_xss_clean($val): $val );
                  } else {
                      $_[ $key ] = ($xss === true) ? $this->_xss_clean($val): $val;
                  }
              }
              
              return $_;
          }
          
          return null;
      }
// -------------------------------------------------------------------------------------
      public function post($name = null, $xss = false)
      {
          if( !array_key_isset('POST', $this->properties) ) {
              return null;
          }

          if( $name !== null && array_key_isset($name, $this->properties['POST']) ) {
              $value = ($xss === true) ? $this->_xss_clean($this->properties['POST'][ $name ])
                                       : $this->properties['POST'][ $name ];
              
              if( isset(self::$handlers['POST'][ $name ]) ) {
                  $closure = self::$handlers['POST'][ $name ];

                  return $closure( $value );
              }
              
              return $value;
              
          } else if( $name == null ) {
              if( empty($this->properties['POST']) ) {
                  return null;
              }
              
              $_ = [];
              
              foreach($this->properties['POST'] as $key=>$val) {
                  if( isset(self::$handlers['POST'][ $key ]) ) {
                      $closure   = self::$handlers['POST'][ $key ];
                      $_[ $key ] = $closure( ($xss === true) ? $this->_xss_clean($val): $val );
                  } else {
                      $_[ $key ] = ($xss === true) ? $this->_xss_clean($val): $val;
                  }
              }
              
              return $_;
          }
          
          return null;
      }
// -------------------------------------------------------------------------------------
      public function get($name = null, $xss = false)
      {
          if( !array_key_isset('GET', $this->properties) ) {
              return null;
          }

          if( $name !== null && array_key_isset($name, $this->properties['GET']) ) {
              $value = ($xss === true) ? $this->_xss_clean($this->properties['GET'][ $name ])
                                       : $this->properties['GET'][ $name ];
              
              if( isset(self::$handlers['GET'][ $name ]) ) {
                  $closure = self::$handlers['GET'][ $name ];

                  return $closure( $value );
              }

              return $value;
              
          } else if( $name === null ) {
              if( empty($this->properties['GET']) ) {
                  return null;
              }
              
              $_ = [];
              
              foreach($this->properties['GET'] as $key=>$val) {
                  if( isset(self::$handlers['GET'][ $key ]) ) {
                      $closure    = self::$handlers['GET'][ $key ];
                      $_[ $key ] = $closure( ($xss === true) ? $this->_xss_clean($val): $val );
                  } else {
                      $_[ $key ] = ($xss === true) ? $this->_xss_clean($val): $val;
                  }
              }
              
              return $_;
          }
          
          return null;
      }
// -------------------------------------------------------------------------------------
      public function files($name = '', $xss = false) 
	  {
          if( !array_key_isset('FILES', $this->properties) ) {
              return false;
          }
	  	  
	      if( empty($name) ) {
              return $this->properties['FILES'];
          }
	        
	      if( !isset($this->properties['FILES'][ $name ]) ) {
		      return false;
		  }
		    
		  if( is_array($this->properties['FILES'][$name]) ) {
			  foreach ($this->properties['FILES'][$name] as $key=>$val) {
			      if( $xss ) {
					  $this->properties['FILES'][$name][$key] = $this->_xss_clean($val);
				  } else {
					  $this->properties['FILES'][$name][$key] = $val;
				  }
			  }
		  } else {
			  return ($xss) ? $this->_xss_clean( $this->properties['FILES'][$name] ) : $this->properties['FILES'][$name];
		  }
		    
		  return $this->properties['FILES'][$name];
	  }
// -------------------------------------------------------------------------------------
      public function requestType()
      {
	      if( !empty($this->server['HTTP_ACCEPT']) ) {
	          $tmp = explode(',', $this->server['HTTP_ACCEPT']);
	          if( !empty($tmp[0]) ) return $tmp[0];
	      }
	      
	      return '';
      }
// -------------------------------------------------------------------------------------
      public function isPost()
      {
		  return ( $this->server['REQUEST_METHOD'] == 'POST' );
      }
// -------------------------------------------------------------------------------------
      public function isGet()
      {
          return ( $this->server['REQUEST_METHOD'] == 'GET' );
      }
// -------------------------------------------------------------------------------------
      protected function _clean_key($str) 
      {
          if( !preg_match("/^[a-z0-9:_\\/-]+$/i", $str) ) {
              wasp_error(
                  "Your request {$str} contains disallowed characters."
              );
          }
            
          return $str;
      }
// -------------------------------------------------------------------------------------
      protected function _clean_val($str) 
      {
          if( is_array($str) ) {
              $_array = [];

              foreach($str as $key=>$val) {
                  $_array[ $this->_clean_key($key) ] = $this->_clean_val($val);
              }

              return $_array;
          }
            
          if( get_magic_quotes_gpc() ) {
              $str = stripslashes($str);
          }
            
          return preg_replace("/\015\012|\015|\012/", "\n", $str);
      }
// -------------------------------------------------------------------------------------
      protected function _xss_clean($data)
      {
          if( is_array($data) ) {
              $_ = [];

              foreach($data as $key=>$val) {
                  $_[ $key ] = $this->_xss_clean($val);
              }
              
              return $_;
          }
          
          // Fix &entity\n;
          $data = str_replace(['&amp;','&lt;','&gt;'], ['&amp;amp;','&amp;lt;','&amp;gt;'], (string)$data);
          $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
          $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
          $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
         
          // Remove any attribute starting with "on" || xmlns
          $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
         
          // Remove javascript: && vbscript: protocols
          $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
          $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
          $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
         
          // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
          $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
          $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
          $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
         
          // Remove namespaced elements (we do not need them)
          $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
         
          do {
              // Remove really unwanted tags
              $old_data = $data;
              $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
          } while( $old_data !== $data );
         
          // we are done...
          return $data;
      }
// -------------------------------------------------------------------------------------
      public static function onGet($name, \Closure $handler)
      {
          if( self::$handlers == null ) self::$handlers = [];
          
          return $this->on($name, 'GET', $handler);
      }
// -------------------------------------------------------------------------------------
      public static function onPost($name, \Closure $handler)
      {
          if( self::$handlers == null ) self::$handlers = [];
          
          return $this->on($name, 'POST', $handler);
      }
// -------------------------------------------------------------------------------------
      public static function on($name, $type, \Closure $handler) 
      {
          if( self::$handlers == null ) self::$handlers = [];
          
          $type = strtoupper($type);

          if( !isset(self::$handlers[ $type ]) || !is_array(self::$handlers[ $type ]) ) {
              self::$handlers[ $type ] = [];
          }
          
          if( !empty($name) ) {
              self::$handlers[ $type ][ $name ] = $handler;
          }
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

  