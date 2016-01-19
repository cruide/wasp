<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

  class WCurl extends \stdClass
  {
      protected
          $_auth_need,
          $_auth_login,
          $_auth_password,

          $_proxy_auth_need,
          $_proxy_auth_login,
          $_proxy_auth_password,
          
          $_post_data,

          $_session,
          $_options,
          $_result,
          $_url;
          
      protected
          $useragent,
          $timeout;
// -------------------------------------------------------------------------------------
      public function __construct()
      {
          if( !function_exists('curl_init') ) {
              wasp_error(
                  'cURL Class - PHP was not built with --with-curl, rebuild PHP to use cURL.'
              );
          }

          $this->timeout    = 30;
          $this->_options   = [];
          $this->useragent  = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1';
          $this->_auth_need = false;
      }
// -------------------------------------------------------------------------------------
      public function setTimeout( $timeout = 30 )
      {
          if( is_integer( $timeout ) ) {
              $this->timeout = $timeout;
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      protected function _init()
      {
          if( !preg_match('!^\w+://! i', $this->_url) ) {
              $this->_url = 'http://' . $this->_url;
          }
          
          $this->_session = curl_init( (!empty($this->_url)) ? $this->_url : '' );
//          curl_setopt($this->_session, CURLOPT_REFERER, BASE_URL);
          curl_setopt($this->_session, CURLOPT_USERAGENT     , (!empty($this->useragent)) ? $this->useragent : 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1' );
          curl_setopt($this->_session, CURLOPT_TIMEOUT       , $this->timeout);
          curl_setopt($this->_session, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($this->_session, CURLOPT_HTTPHEADER    , ['Expect:']); 
          
          if( $this->_auth_need ) {
              curl_setopt($this->_session, CURLOPT_USERPWD, $this->_auth_login . ':' . $this->_auth_password);
          }
          
          if( !empty($this->_options) ) {
              foreach($this->_options as $key=>$val) {
                  curl_setopt($this->_session, $key, $val);
              }
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function disableHttpAuth()
      {
          $this->_auth_need = false;
      }
// -------------------------------------------------------------------------------------
      public function enableHttpAuth()
      {
          if( !empty($this->_auth_login) && !empty($this->_auth_password) ) {
              $this->_auth_need = false;
          }
      }
// -------------------------------------------------------------------------------------
      public function setHttpAuth($username = '', $password = '')
      {
          if( !empty($username) && !empty($password) ) {
              $this->_auth_login    = $username;
              $this->_auth_password = $password;
              $this->_auth_need     = true;
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function setPostData( $data = null )
      {
          if( !empty($data) && is_array($data) ) {
              $data = http_build_query($data);
          }
      }
// -------------------------------------------------------------------------------------
      public function option($name, $value)
      {
          if( !empty($name) && !empty($value) ) {
              $this->_options[ $name ] = $value;
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function execute()
      {
          $this->_init();
          $this->_result = curl_exec( $this->_session );
          
          if( curl_errno($this->_session) ) { 
          	  return curl_error($this->_session);
          }
          
          if( !empty($this->_result) ) { 
          	  return $this->_result;
          }
          
          return false;
      }
// -------------------------------------------------------------------------------------
      public function proxy($url, $port = 8080)
      {
          if( !empty($url) && is_numeric($port) ) {
              $this->option(CURLOPT_HTTPPROXYTUNNEL, true);
              $this->option(CURLOPT_PROXY, "{$url}:80");
          }
        
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function proxyLogin($username = '', $password = '')
      {
          if( !empty($username) && !empty($password) ) {
              $this->option(CURLOPT_PROXYUSERPWD, "{$username}:{$password}");
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function get($url = '')
      {
          if( !empty($url) ) {
              $this->_url = $url;
          }
          
          if( !empty($this->_url) ) {
              
              if( isset($this->_options[ CURLOPT_POST ]) ) {
                  unset(
                      $this->_options[ CURLOPT_POST ], 
                      $this->_options[ CURLOPT_POSTFIELDS ]
                  );
              }
              
              $this->_options[ CURLOPT_RETURNTRANSFER ] = true;

              return $this->execute();
          }
          
          return false;
      }
// -------------------------------------------------------------------------------------
      public function post($params = [], $url = '')
      {
          if( !empty($params) && is_array($params) ) {
              $params = http_build_query($params);
          }
          
          if( !empty($url) ) {
              $this->_url = $url;
          }

          unset( $this->_options[ CURLOPT_RETURNTRANSFER ] );
          
          $this->_options[ CURLOPT_POST ]       = true;
          $this->_options[ CURLOPT_POSTFIELDS ] = $params;

          if( !empty($this->_url) ) {
              return $this->execute();
          }
          
          return false;
      }
// -------------------------------------------------------------------------------------
      public function setCookie( array $params = [] ) 
      {
          if( !empty($params) ) {
              if( is_array($params) ) {
                  $params = http_build_query($params);
              }

              $this->option(CURLOPT_COOKIE, $params);
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function getInfo()
      {
		  if( empty($url) ) {
			  return false;
		  }
		  
		  return curl_getinfo($this->_session);
      }
// -------------------------------------------------------------------------------------
      function __destruct()
      {
          if( !empty($this->_session) ) curl_close($this->_session);
      }
  }
