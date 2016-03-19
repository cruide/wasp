<?php if( !defined('__FUNCTIONS__') ) { define('__FUNCTIONS__', true);
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

  function wasp_autoloader($class_name)
  {
      if( preg_match('#App\|Library#is', (string)str_replace('\\', '|', $class_name)) ) {
          $lib_path  = str_replace('App\\Library\\', '', $class_name);
          $file_path = LIBRARY_DIR . DIR_SEP . str_replace('\\', DIR_SEP, $lib_path) . '.php';
          
          if( is_file($file_path) ) {
              try {
                  require($file_path);
              } catch( \Exception $e ) {
                  wasp_error( $e->getMessage() );
              }
              
              return;
          }
      }
      
      $name      = array_get_last( explode('\\', $class_name) );
      $filename  = snake_case($name);
      $class_name = preg_replace("/{$name}$/", $filename, $class_name);
      $file_path = ROOT . DIR_SEP .  strtolower( str_replace('\\', DIR_SEP, $class_name) ) . '.php';
      
      try {
          require($file_path);
      } catch( \Exception $e ) {
          wasp_error( $e->getMessage() );
      }
  }
// ------------------------------------------------------------------------------
  function get_http_referer( $clear = true )
  {
      global $_SERVER;
      
      if( !isset($_SERVER['HTTP_REFERER']) ) {
          return false;
      }

      if( $clear === false ) {
          return $_SERVER['HTTP_REFERER'];
      }
      
      $_ = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
      
      return (!empty($_)) ? trim($_) : false;
  }  
// ------------------------------------------------------------------------------
  /**
  * Corretion for directory path
  * 
  * @param string $dir
  * @return string
  */
  function path_correct( $dir, $end_slash = false )
  {
      if( empty($dir) ) return $dir;
      
      $dir = str_replace(':', '||'   , $dir);
      $dir = str_replace('\\\\', '::', $dir);
      $dir = str_replace('\\', '::'  , $dir);
      $dir = str_replace('//', '::'  , $dir);
      $dir = str_replace('/', '::'   , $dir);
      $dir = str_replace('::', '/'   , $dir);
      $dir = str_replace('||', ':'   , $dir);
      
      if( $end_slash && !preg_match("#(.*)\/$#is" , $dir, $tmp) ) {
          $dir = $dir . '/';
      }
      
      return $dir;
  }

// ------------------------------------------------------------------------------

  function wasp_mkdir($path, $mode = 0755)
  {
      $path = path_correct($path);
      
      if( !empty($path) && is_string($path) && !is_dir($path) ) {
          if( !mkdir($path, $mode, true) ) {
              wasp_error(
                  'Can not create directory: ' . $path,
                  500
              );
          }
          
          @chmod($path, $mode);
      }
  }
  
// ------------------------------------------------------------------------------
  /**
  * Return IP adddress of client
  * 
  */
  function get_ip_address() 
  {
      global $_SERVER;
       
      if( !empty($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['HTTP_CLIENT_IP']) ) {
          $ipaddr = $_SERVER['HTTP_CLIENT_IP'];
      } else if( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
          $ipaddr = $_SERVER['HTTP_CLIENT_IP'];
      } else if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
          $ipaddr = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } else {
          $ipaddr = $_SERVER['REMOTE_ADDR'];
      }
        
      if( $ipaddr === false ) {
          return '0.0.0.0';
      }
        
      if( strstr($ipaddr, ',') ) {
          $x = explode(',', $ipaddr);
          $ipaddr = end( $x );
      }
        
      if( filter_var($ipaddr, FILTER_VALIDATE_IP) === false ) {
          $ipaddr = '0.0.0.0';
      }
        
      return $ipaddr;
  }

// -------------------------------------------------------------------------------------
  /**
  * Check URL
  * 
  * @param string $url
  */
  function is_url_exists($url)
  {
  	  if( !filter_var($url, FILTER_VALIDATE_URL) ) return false;
      
	  $hdrs = @get_headers($url);
	  if( isset($hdrs[0]) && preg_match('/HTTP\/1\.[0-1]\s+\d{3}/is', $hdrs[0]) ) return true;
      
	  return false;
  }

// -------------------------------------------------------------------------------------
  /**
  * Sending header to disable caching pages
  * 
  */
  function http_cache_off()
  {
      if( !headers_sent() ) {
          header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
          header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
          header('Cache-Control: no-cache, must-revalidate');
          header('Cache-Control: post-check=0,pre-check=0', false);
          header('Cache-Control: max-age=0', false);
          header('Pragma: no-cache');
      }
  }

// -------------------------------------------------------------------------------------
  /**
  * extract the fractional part of a fractional number
  * 
  * @param mixed $num
  * @return mixed
  */
  function fract($num)
  {
      if( is_numeric($num) ) {
          $num -= floor( (float)$num );
          return (int)str_replace('0.', '', (string)$num);
      }
        
      return null;
  }

// -------------------------------------------------------------------------------------

  function float_extract($num)
  {
      return ( is_float($num) ) ? explode('.', (string)$num) : false;
  }

// -------------------------------------------------------------------------------------
  /**
  * проверка на четность
  */
  function is_even($num)
  {
      if( !is_numeric($num) ) return false;
      if( !preg_match('/[\.]/s', $num) ) {
          return ( fract($num) & 1 ) ? false : true;
      } else {
          return ( $num & 1 ) ? false : true;
      }
  }
  
// ------------------------------------------------------------------------------

  function escape($str, $escapemethod = 'htmlspecialchars')
  {
      if( array_count($str) > 0 ) {
          $_ = [];
          foreach($str as $key=>$val) {
              $_[ $key ] = escape($val);
          }
          return $_;
      } else if( is_string($str) ) {
          if( in_array($escapemethod, ['htmlspecialchars', 'htmlentities']) ) {
              return call_user_func($escapemethod, $str, ENT_COMPAT, 'UTF-8');
          }
          
          return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
      }
      
      return $str;
  }    
  
// ------------------------------------------------------------------------------

  function unescape($str)
  {
      if( is_array($str) && count($str) ) {
          $_ = [];
          foreach($str as $key=>$val) {
              $_[ $key ] = unescape($val);
          }
          return $_;
      } else if( is_string($str) ) {
          return htmlspecialchars_decode($str, ENT_QUOTES);
      }
      
      return $str;
  }
  
// ------------------------------------------------------------------------------
  /**
  * Генератор соли
  * 
  * @return string $lenght
  */
  function salt_generation( $lenght = 18 )
  {
      return substr( sha1(mt_rand()), 0, $lenght );
  }

// ------------------------------------------------------------------------------
  /**
  * Шифрование пароля
  * 
  * @param string $password
  * @param string $salt
  */
  function password_crypt( $password, $salt = null )
  {
      if( empty($password) ) { return false; }
      if( null == $salt )   { $salt = CORE_NAME; }
    
      return crypt( md5($password), '$1$' . $salt . '$' );
  }

// ------------------------------------------------------------------------------
  /**
  * Генератор паролей
  * 
  * @param int $number
  */
  function password_generate( $number = 8 )
  {
      $arr = [
          'a','b','c','d','e','f','g','h','i','j','k','l',
          'm','n','o','p','r','s','t','u','v','x','y','z',
          'A','B','C','D','E','F','G','H','I','J','K','L',
          'M','N','O','P','R','S','T','U','V','X','Y','Z',
          '1','2','3','4','5','6','7','8','9','0',
      ];

      $pass = '';
      
      for($i = 0; $i < $number; $i++) {
        $pass .= $arr[ rand(0, count($arr) - 1) ];
      }
      
      return $pass;
  }
 
// ------------------------------------------------------------------------------
  /**
  * Метод обратимого шифрования
  * 
  * @param mixed $string
  * @param mixed $key
  */
  function encrypt($string, $key = null) 
  {
      // Алгоритм ключа (KSA)
      $s   = [];        
      $key = (null === $key) ? md5( CORE_NAME ) : (string)$key;
      
      for( $i = 0; $i < 256; $i++ ) {
          $s[$i] = $i;
      }        

      $j = 0;
      $x = null;

      for( $i = 0; $i < 256; $i++ ) {
          $j       = ( $j + $s[$i] + ord($key[$i % strlen($key)]) ) % 256;
          $x       = $s[ $i ];
          $s[ $i ] = $s[ $j ];
          $s[ $j ] = $x;
      }

      // Алгоритм шифрования псавдо-случайной генерации (PRGA)
      $cipher = '';
      $i = $j = $y = 0;

      for($y = 0; $y < strlen($string); $y++ ) {
          $i       = ( $i + 1 ) % 256;
          $j       = ( $j + $s[$i] ) % 256;
          $x       = $s[ $i ];
          $s[ $i ] = $s[ $j ];
          $s[ $j ] = $x;
          
          $cipher .= $string[$y] ^ chr( $s[ ($s[$i] + $s[$j]) % 256 ] );
      }

      return $cipher;
  }
  
// ------------------------------------------------------------------------------
  /**
  * Дешифровка после метода encrypt
  * 
  * @param mixed $cipher
  * @param mixed $key
  */
  function decrypt($cipher, $key = null) 
  {
      return encrypt($cipher, (null === $key) ? md5( CORE_NAME ) : (string)$key );
  }  
  
// ------------------------------------------------------------------------------

  function str_base64_encrypt($str)
  {
      return base64_encode( encrypt($str, 'string_encryption') );
  }
  
// ------------------------------------------------------------------------------

  function str_base64_decrypt($str)
  {
      return decrypt( base64_decode($str), 'string_encryption' );
  }
  
// ------------------------------------------------------------------------------
  /**
  * Convert array to object
  * 
  * @param array $_array
  * @return wobject
  */
  function array_to_object( $_array )
  {
	  if( array_count($_array) > 0 ) {
		  return new \Wasp\stdObject($_array);
	  }

	  return new \Wasp\stdObject();
  }
  
// -------------------------------------------------------------------------------------
  /**
  * Get settings from INI files
  * 
  * @param string $name
  */
  function cfg($name)
  {
      static $settings;

      if( empty($settings) ) {
          $settings = new \Wasp\stdObject();
      }

      if( isset($settings->$name) ) {
          return $settings->$name;
      }
            
      if( isset($name) && is_file(SETTINGS_DIR . DIR_SEP . (string)$name . '.ini') ) {
          try {
              $tmp = parse_ini_file( SETTINGS_DIR . DIR_SEP . (string)$name . '.ini', true );
          } catch( \Wasp\Exception $e ) {
              wasp_error( $e->getMessage() );
          }
          
          if( array_count($tmp) > 0 ) {
              $ini = new \Wasp\stdObject();
              
              foreach($tmp as $key=>$val) {
                  if( array_count($val) > 0 ) {
                      $section = str_replace( ['.', '-'], '_', strtolower($key) );
                      $data    = new \Wasp\stdObject();
                      
                      foreach($val as $k=>$v) {
                          if( is_varible_name($k) ) {
                              $v = str_replace( 
                                  [ '%TEMP_DIR%', '%ROOT_DIR%', '%BASE_URL%', '%LOGS_DIR%' ],
                                  [ TEMP_DIR, ROOT, BASE_URL, LOGS_DIR ],
                                  $v
                              );
                              
                              $data->$k = $v;
                          }
                      }
                      
                      $ini->$section = $data;
                  } else {
                      $varible       = str_replace( '.', '_', strtolower($key) );
                      $ini->$varible = $val;
                  }
              }
              
              $settings->$name = $ini;
              unset($key, $val, $section, $k, $v, $data, $tmp);
              
              return $ini;
          }
      }
      
      return new \Wasp\stdObject();
  }
  
// -------------------------------------------------------------------------------------
  /**
  * Check controller
  * 
  * @param string $module
  * @param string $controller
  * @return bool
  */
  function is_controller_exists($controller)
  {
      $controller = snake_case($controller, '_');

	  if( is_file( CONTROLLERS_DIR . DIR_SEP . "{$controller}.php" ) ) {
		  return true;
	  }
	  
	  return false;
  }
  
// -------------------------------------------------------------------------------------
  /**
  * Show system error
  * 
  * @param string $message
  * @param integer $status
  * @param string $title
  */
  function wasp_error($message, $file = null, $line = null, $status = 500)
  {
      if( is_ajax() ) {
          $html = "<div id=\"error-header\" style=\"font-size: 10pt; font-weight: bold;\">" . CORE_NAME . " system error</div><br />" .
                  "<div id=\"error-content\" style=\"font-size: 8pt;\">" .
                  ((!empty($file)) ? "<div><strong>File:</strong> {$file}</div>" : '' ) .
                  ((!empty($line)) ? "<div><strong>Line:</strong> {$line}</div>" : '' ) .
                  "{$message}</div>";
      } else {
          set_http_status($status);
          
          $html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' .
                  "<meta name=\"generator\" content=\"" . FRAMEWORK . "\" /><meta name=\"description\" content=\"" . CORE_NAME . " system error\" />" .
                  "<title>" . CORE_NAME . " system error</title>\n<style>\nbody { font-family: Tahoma, Arial, sans-serif; fint-size: 8pt; }</style></head><body>" .
                  "<div id=\"error-header\" style=\"font-size: 14pt; color: red;\">" . CORE_NAME . " system error</div><br /><div id=\"error-content\" style=\"font-size: 10pt;\">" .
                  ((!empty($file)) ? "<div><strong>File:</strong> {$file}</div>" : '' ) .
                  ((!empty($line)) ? "<div><strong>Line:</strong> {$line}</div>" : '' ) .
                  "{$message}</div>" .
                  '<div id="error-footer" style="margin-top: 10px; border-top: 1px solid gray; font-size: 8pt;">' .
                  '<span style="color: gray;">' . FRAMEWORK . ', Copyright &copy; 2014 Tishchenko A., All rights reserved.</span></div></body></html>';
      }
              
      error_log( $message, 3, LOGS_DIR . DIR_SEP . 'app_errors' );

      exit($html);
  }
// -------------------------------------------------------------------------------------
  function get_x_response_type()
  {
      global $_SERVER;
      
      return $_SERVER['HTTP_X_RESPONSE_TYPE'];
  }
// -------------------------------------------------------------------------------------
  /**
  * Redirector
  * 
  * @param array $data
  * @param integer $status
  */
  function redirect($data = null)
  {
      if( !is_array($data) ) {
          if( is_string($data) ) {
              header( 'Location: ' . make_url($data), true, 301);
              exit();
              
          } else if( empty($data) ) {
              header('Location: ' . BASE_URL);
              exit();
          }
          
          wasp_error('Incorrect redirect path');
      }

      $msgTemp = [];
      
      if( !empty($data['message']) ) { $msgTemp['message'] = $data['message']; }
      if( !empty($data['error']) ) { $msgTemp['error'] = $data['error']; }
      if( array_count($msgTemp) > 0 ) { make_temp( 'redirect', serialize($msgTemp) ); }
      
      unset($data['message'], $data['error'], $msgTemp);
      
      if( empty($data['controller']) ) {
          unset($data['controller'], $data['method']);
          
          $_ = '';
          
          if( array_count($data) > 0 ) {
              foreach($data as $key=>$val) {
                  if( is_varible_name($key) && is_scalar($val) ) {
                      $_ .= "/{$key}/{$val}";
                  }
              }
          }
          
          header( 'Location: ' . BASE_URL . ((!empty($_)) ? '/index/default' . $_ : '') );
          exit();
      }
      
      $_ = BASE_URL . '/' . $data['controller'];
      
      if( isset($data['method']) ) {
          $_ .= '/' . $data['method'];
      }

      unset($data['controller'], $data['method']);
      
      if( array_count($data) > 0 ) {
          foreach($data as $key=>$val) {
              if( is_varible_name($key) && is_scalar($val) ) {
                  $_ .= "/{$key}/{$val}";
              }
          }
      }
      
      if( is_ajax() && !headers_sent() ) {
          http_cache_off();

          if( get_x_response_type() == 'html' ) {
              exit( 
                javascript('window.location.href = "' . $_ . '";')
              );
          } else if( get_x_response_type() == 'script' ) {
              exit('window.location.href = "' . $_ . '";');
          }
      }
      
      header( 'Location: ' . $_, true, 301);
      exit();
  }

// ------------------------------------------------------------------------------
  /**
  * Make correct URL
  * 
  * @param string $url
  * @param bool $add_suf
  */
  function make_url($url, $add_suf = true) 
  {
      if( empty($url) || !is_scalar($url) ) return false;
      if( preg_match('#^(http|https|ftp)://(.*?)#i', $url) ) return $url;
      if( !preg_match('/^\//', $url) ) {
          $url = '/' . $url;
      }
      
      $config = cfg('config')->application;
      $suffix = !empty($config->url_suffix) ? (string)$config->url_suffix : 'html';
      $url    = preg_replace('#^([a-z0-9\/\.\_\-]+)\/$#isu', '$1', $url);
      
      if( $add_suf && !preg_match('/^(\w+)\.' . $suffix . '$/is', $url) ) { 
          $url = "{$url}.{$suffix}";
      }
      
      return BASE_URL . $url;
  }
  
// ------------------------------------------------------------------------------
  /**
  * Get ststus by code number
  * 
  * @param integer $code
  */
  function get_http_status($code)
  {
      if( !is_numeric($code) ){ return false;}
    
      $_HTTP_STATUS = [
            100 => '100 Continue',
            101 => '101 Switching Protocols',
            102 => '102 Processing',
            200 => '200 OK',
            201 => '201 Created',
            202 => '202 Accepted',
            203 => '203 Non-SB_Authoritative Information',
            204 => '204 No Content',
            205 => '205 Reset Content',
            206 => '206 Partial Content',
            207 => '207 Multi Status',
            226 => '226 IM Used',
            300 => '300 Multiple Choices',
            301 => '301 Moved Permanently',
            302 => '302 Found',
            303 => '303 See Other',
            304 => '304 Not Modified',
            305 => '305 Use Proxy',
            306 => '306 (Unused)',
            307 => '307 Temporary Redirect',
            400 => '400 Bad Request',
            401 => '401 Unauthorized',
            402 => '402 Payment Required',
            403 => '403 Forbidden',
            404 => '404 Not Found',
            405 => '405 Method Not Allowed',
            406 => '406 Not Acceptable',
            407 => '407 Proxy nCore_Authentication Required',
            408 => '408 Request Timeout',
            409 => '409 Conflict',
            410 => '410 Gone',
            411 => '411 Length Required',
            412 => '412 Precondition Failed',
            413 => '413 Request Entity Too Large',
            414 => '414 Request-URI Too Long',
            415 => '415 Unsupported Media Type',
            416 => '416 Requested Range Not Satisfiable',
            417 => '417 Expectation Failed',
            420 => '420 Policy Not Fulfilled',
            421 => '421 Bad Mapping',
            422 => '422 Unprocessable Entity',
            423 => '423 Locked',
            424 => '424 Failed Dependency',
            426 => '426 Upgrade Required',
            449 => '449 Retry With',
            500 => '500 Internal Server Error',
            501 => '501 Not Implemented',
            502 => '502 Bad Gateway',
            503 => '503 Service Unavailable',
            504 => '504 Gateway Timeout',
            505 => '505 HTTP Version Not Supported',
            506 => '506 Variant Also Varies',
            507 => '507 Insufficient Storage',
            509 => '509 Bandwidth Limit Exceeded',
            510 => '510 Not Extended'
      ];
    
      if( !empty($_HTTP_STATUS[$code]) ) {
          return $_HTTP_STATUS[$code];
      }
    
      return false;
  }
// --------------------------------------------------------------------------------
  /**
  * Set HTTP status
  * 
  * @param integer $code
  */
  function set_http_status($code)
  {
      $status = get_http_status($code);
      
      if( $status != false && !headers_sent() ) {
          header('HTTP/1.1 ' . $status);
          header('Status: ' . $status);

          return true;
      }
      
      return false;
  }
// --------------------------------------------------------------------------------
  /**
  * Check for ajax request
  * 
  */
  function is_ajax()
  {
  	  global $_SERVER;
  	  
	  if( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
	      return true; 
	  } else if( !empty($_SERVER['X_REQUESTED_WITH']) && $_SERVER['X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
	      return true;
	  } else if( !empty($_SERVER['HTTP_ACCEPT']) && (false !== strpos($_SERVER['HTTP_ACCEPT'], 'text/x-ajax')) ) {
	      return true;
	  }
	  
	  return false;
  }
// ------------------------------------------------------------------------------
  /**
  * Return files list
  * Support files masks
  * 
  * @param string $path
  * @param array $extentions
  * @param bool $files_only
  * @return []
  */
  function get_files_list($path, $extentions = [], $files_only = false)
  {
      if( empty($path) || !is_dir($path) ) {
          return false;
      }
      
      if( array_count($extentions) > 1 ) {
          $extentions = '*.{' . implode(',', $extentions) . '}';
          $flag       = GLOB_BRACE;
      } else if( array_count($extentions) == 1 ) {
          $extentions = '*.' . reset($extentions);
          $flag       = null;
      } else {
          $extentions = '*';
          $flag       = null;
      }

      $files = glob( $path . DIR_SEP . $extentions , $flag);
      
      if( !empty($files) ) {
          $_result = [];
          
          foreach($files as $key=>$val) {
              if( is_file($val) ) {
              	  if( !$files_only ) {
              	  	  $_result[ str_replace($path . DIR_SEP, '', $val) ] = filesize($val);
				  } else {
					  $_result[] = str_replace($path . DIR_SEP, '', $val);
				  }
              }
          }
          
          return $_result;
      }
      
      return false;
  }
// ------------------------------------------------------------------------------
  /**
  * return directory list by path
  * 
  * @param string $path
  * @return []
  */
  function get_directory_list($path)
  {
      if( empty($path) || !is_dir($path) ) {
          return false;
      }
      
      $search_path = $path;
      
      if( !preg_match('#^(.*)' . DIR_SEP . '$#', $path) ) {
          $search_path = $path = $path . DIR_SEP;
      }

      if( !preg_match('#^(.*)\*$#', $search_path) ) {
          $search_path = $search_path . '*';
      }
      
      $dirs = glob( $search_path , GLOB_ONLYDIR);
      
      if( !empty($dirs) ) {
          $_result = array();
          
          foreach($dirs as $key=>$val) {
              if( is_dir($val) ) {
                  $_result[] = str_replace($path, '', $val);
              }
          }
          
          return $_result;
      }
      
      return false;
  }
// ------------------------------------------------------------------------------
  /**
  * Show 404 error message
  * 
  */
  function show_error_404()
  {
	  if( is_controller_exists('errors') ) {
		  redirect([
              'controller' => 'errors',
              'method'     => 'show404',
          ]);
	  }
	  
	  wasp_error('404 Not found', null, null, 404);
  }
// ------------------------------------------------------------------------------
  /**
  * Show 403 error message
  * 
  */
  function show_error_403()
  {
      if( is_controller_exists('errors') ) {
          redirect([
              'controller' => 'errors',
              'method'     => 'forbidden',
          ]);
      }
      
      wasp_error('403 Forbidden', null, null, 403);
  }
// ------------------------------------------------------------------------------
  /**
  * Show 401 error message
  * 
  */
  function show_error_401()
  {
      if( is_controller_exists('errors') ) {
          redirect([
              'controller' => 'errors',
              'method'     => 'unauthorized',
          ]);
      }
      
      wasp_error('401 Unauthorized', null, null, 401);
  }

// ------------------------------------------------------------------------------
   /**
   * Создать временный файл
   * 
   * @param string $temp_name
   * @param mixed $content
   * @param integer $keep
   */
  function make_temp($temp_name, $content, $keep = 60)
  {
	  if( empty($temp_name) ) return false;
	  
	  $file_name = $temp_name . '_' . session()->id(true) . '.tmp';
	  $tmp       = new \Wasp\Temp($file_name, TEMP_DIR);

	  return $tmp->setContent([
	      'content' => $content,
	      'keepto'  => time() + (int)$keep,
	  ])->write();
  }
  
// ------------------------------------------------------------------------------
  /**
  * Забрать темп (с удалением)
  * 
  * @param string $temp_name
  */
  function pickup_temp($temp_name)
  {
	  if( empty($temp_name) ) return null;

	  $file_name = $temp_name . '_' . session()->id(true) . '.tmp';
	  
	  if( !is_file(TEMP_DIR . DIR_SEP . $file_name) ) return null;
	  
	  $tmp = new \Wasp\Temp($file_name, TEMP_DIR);
	  $_   = $tmp->read();
	  $tmp->delete();
	  
	  return ( time() > (int)$_['keepto'] ) ? null : $_['content'];
  }

// ------------------------------------------------------------------------------

  function wasp_ucfirst( $str )
  {
      if( function_exists('mb_strlen') ) {
          return mb_strtoupper( mb_substr( $str, 0, 1, 'UTF-8' ), 'UTF-8' ) . mb_substr( $str, 1, mb_strlen( $str ), 'UTF-8' );
      }

      if( function_exists('iconv') ) {
          $result = iconv('utf-8', 'windows-1251', $str);
          return iconv('windows-1251', 'utf-8', ucfirst( $result ) );
      }
      
      return ucfirst( $str );
  }

// ------------------------------------------------------------------------------

  function wasp_strlen( $str )
  {
      if( function_exists('mb_strlen') ) return mb_strlen($str, 'UTF-8');
      if( function_exists('iconv') ) {
          $result = iconv('utf-8', 'windows-1251', $str);
          return strlen( $result );
      }
      
      return strlen( $str );
  }

// ------------------------------------------------------------------------------

  function wasp_strtolower( $str )
  {
      if( function_exists('mb_strtolower') ) return mb_strtolower($str, 'UTF-8');
      if( function_exists('iconv') ) {
          $result = iconv('utf-8', 'windows-1251', $str);
          return iconv('windows-1251', 'utf-8', strtolower( $result ) );
      }

      return strtolower( $str );
  }

// ------------------------------------------------------------------------------

  function wasp_strtoupper( $str )
  {
      if( function_exists('mb_strtoupper') ) return mb_strtoupper($str, 'UTF-8');
      if( function_exists('iconv') ) {
          $result = iconv('utf-8', 'windows-1251', $str);
          return iconv('windows-1251', 'utf-8', strtoupper( $result ) );
      }

      return strtoupper( $str );
  }

// ------------------------------------------------------------------------------

  function wasp_strstr($haystack, $needle, $part = false)
  {
      return ( function_exists('mb_strstr') ) ? mb_strstr($haystack, $needle, $part, 'UTF-8') : strstr($haystack, $needle, $part);
  }

// ------------------------------------------------------------------------------

  function crop_string($string, $lenght)
  {
      $string = strip_tags($string);

      if( function_exists('mb_strlen') ) {
          $len    = (mb_strlen($string) > $lenght) ? mb_strripos( mb_substr($string, 0, $lenght), ' ' ) : $lenght;
          $result = mb_substr($string, 0, $len);
          
          return (mb_strlen($string) > $lenght) ? $result . '...' : $result;
      }

      if( function_exists('iconv') ) {
          $result = iconv('utf-8', 'windows-1251', $string);
          $length = strripos( substr($result, 0, $length), ' ');
          
          return iconv('windows-1251', 'utf-8', substr($result, 0, $length) );
      }
      
      $len    = (strlen($string) > $lenght) ? strripos( substr($string, 0, $lenght), ' ' ) : $lenght;
      $result = substr($string, 0, $len);
      
      return (mb_strlen($string) > $lenght) ? $result . '...' : $result;
  }

// ------------------------------------------------------------------------------

  function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
  {
      if ($length == 0) {
          return '';
      }

      if( function_exists('mb_substr') ) {
          if( mb_strlen($string, 'UTF-8') > $length ) {
              $length -= min($length, mb_strlen($etc, 'UTF-8'));

              if( !$break_words && !$middle ) {
                  $string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length + 1, 'UTF-8'));
              }
             
              if( !$middle ) {
                  return mb_substr($string, 0, $length, 'UTF-8') . $etc;
              }
    
              return mb_substr($string, 0, $length / 2, 'UTF-8') . $etc . mb_substr($string, - $length / 2, $length, 'UTF-8');
          }
    
         return $string;
     }

     // no MBString fallback
     if( isset($string[ $length ]) ) {
         $length -= min($length, strlen($etc));
         
         if( !$break_words && !$middle ) {
             $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));
         }
         if( !$middle ) {
             return substr($string, 0, $length) . $etc;
         }
   
         return substr($string, 0, $length / 2) . $etc . substr($string, - $length / 2);
     }

     return $string;
  } 
  
// ------------------------------------------------------------------------------
  /**
  * Generate a unique key
  */
  function uuid()
  {
      return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
          mt_rand( 0, 0x0fff ) | 0x4000,
          mt_rand( 0, 0x3fff ) | 0x8000,
          mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
      );
  }

// ------------------------------------------------------------------------------
  /**
  * Checking the unique key
  * 
  * @param string $uuid
  */
  function is_uuid($uuid)
  {
      return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
  }
  
// ------------------------------------------------------------------------------

  function wasp_date_format($string, $format = null, $default_date = '', $formatter = 'auto')
  {
      if( $format === null ) {
          $format = '%b %e, %Y';
      }
      
      if( $string != '' && $string != '0000-00-00' && $string != '0000-00-00 00:00:00' ) {
          $timestamp = wasp_make_timestamp($string);
      } else if( $default_date != '' ) {
          $timestamp = wasp_make_timestamp($default_date);
      } else {
          return;
      } 
      
      if( $formatter == 'strftime' || ($formatter == 'auto' && strpos($format,'%') !== false) ) {
          if( DIRECTORY_SEPARATOR == '\\' ) {
              $_win_from = ['%D', '%h', '%n', '%r', '%R', '%t', '%T'];
              $_win_to   = ['%m/%d/%y', '%b', "\n", '%I:%M:%S %p', '%H:%M', "\t", '%H:%M:%S'];

              if( strpos($format, '%e') !== false ) {
                  $_win_from[] = '%e';
                  $_win_to[]   = sprintf('%\' 2d', date('j', $timestamp));
              } 
              
              if( strpos($format, '%l') !== false ) {
                  $_win_from[] = '%l';
                  $_win_to[]   = sprintf('%\' 2d', date('h', $timestamp));
              } 
              
              $format = str_replace($_win_from, $_win_to, $format);
          } 
          
          return strftime($format, $timestamp);
      } else {
          return date($format, $timestamp);
      }
  } 

// ------------------------------------------------------------------------------

  function wasp_make_timestamp($string)
  {
      if( empty($string) ) {
          return time();
      } else if( $string instanceof \DateTime ) {
          return $string->getTimestamp();
      } else if( strlen($string) == 14 && ctype_digit($string) ) {
          return mktime( substr($string, 8, 2), substr($string, 10, 2), substr($string, 12, 2),
                         substr($string, 4, 2), substr($string, 6, 2), substr($string, 0, 4));
      } else if( is_numeric($string) ) {
          return (int) $string;
      } else {
          $time = strtotime($string);

          if( $time == -1 || $time === false ) {
              return time();
          }
          
          return $time;
      }
  }
  
// ------------------------------------------------------------------------------
  /**
  * Backup file by GZ
  * 
  * @param string $file_in
  * @param string $file_out
  */
  function gz_file_pack($file_in, $file_out = null)
  {
      if( !is_file($file_in) ) {
          return false;
      }

      $content = file_get_contents($file_in);
    
      if( !isset($file_out) ) {
          $file_out = $file_in . '.gz';
      }
                                    
      if( $gz = gzopen($file_out, 'w9') ) {
          gzwrite($gz, $content);
          gzclose($gz);
          return true;
      } else {
          return false;
      }
  }

// ------------------------------------------------------------------------------

  function javascript( $str )
  {
      return "<script type=\"text/javascript\">\n{$str}\n</script>";
  }

// ------------------------------------------------------------------------------

  function obj_to_array( $obj )
  {
      if( !is_object($obj) ) return false;
      
      $publics = function($obj) {
          return get_object_vars($obj);
      };
      
      $values = $publics($obj);
      
      foreach($values as $key=>$val) {
          if( is_object($val) ) $values[ $key ] = obj_to_array($val);
      }
      
      return $values;
  }

// ------------------------------------------------------------------------------

  function get_object_public_vars($obj)
  {
      if( !is_object($obj) ) {
          return null;
      }
      
      return get_object_vars($obj);
  }
  
// ------------------------------------------------------------------------------
  /**
  * Получение первого элемента масива
  * Если $key = true, то вернется array с ключем
  * и значением первого элемента
  * 
  * @param array $_array
  * @param bool $key
  */
  function array_get_first( $_array, $key = false ) 
  {
      if( array_count($_array) <= 0 ) return false;

      reset($_array);

      $k       = key($_array);
      $element = $_array[ $k ];
        
      unset($_array);
        
      if( $key === true ) return [ $k => $element ];
        
      return $element;
  }

// ------------------------------------------------------------------------------
  /**
  * Получение ключа первого элемента масива
  * 
  * @param array $_array
  */
  function array_get_first_key( $_array ) 
  {
      if( array_count($_array) <= 0 ) return false;

      reset($_array);
      $k = key($_array);
      
      unset($_array);
        
      return $k;
  }
  
// ------------------------------------------------------------------------------
  /**
  * Получение последнего элемента масива
  * Если $key = true, то вернется array с ключем
  * и значением последнего элемента
  * 
  * @param array $_array
  * @param bool $key
  */
  function array_get_last( $_array, $key = false ) 
  {
      if( array_count($_array) <= 0 ) return false;

      end($_array);

      $k       = key($_array);
      $element = $_array[ $k ];
      
      unset($_array);
                                                  
      if( $key === true ) return [ $k => $element ];
        
      return $element;
  } 

// ------------------------------------------------------------------------------
  /**
  * Получение ключа последнего элемента масива
  * 
  * @param array $_array
  */
  function array_get_last_key( $_array ) 
  {
      if( array_count($_array) <= 0 ) return false;

      end($_array);
      $k = key($_array);

      unset($_array);
      
      return $k;
  } 

// ------------------------------------------------------------------------------
  /**
  * Getting the number of entries in the array. 
  * If this is not an array, it returns 0
  * 
  * @param array $_
  * @param mixed $mode
  * @return integer
  */
  function array_count($_, $mode = null)
  {
      return (!is_array($_)) ? 0 : count($_, $mode);
  }

// ------------------------------------------------------------------------------
  /**
  * A faster version of the check for key in the array
  * 
  * @param string $key
  * @param array $_array
  * @return bool
  */
  function array_key_isset($key, $_array)
  {
      if( !is_array($_array) ) return false;
      
      return (isset($_array[ $key ]) || array_key_exists($key, $_array));
  }
  
// ------------------------------------------------------------------------------
  /**
  * Forced cleaning memory
  */
  function memory_clear()
  {
      if( function_exists('gc_collect_cycles') ) {
          gc_enable();
          $_ = gc_collect_cycles();
          gc_disable();
          
          return (int)$_;
      }
      
      return 0;
  }
  
// ------------------------------------------------------------------------------
  /**
  * Get max memoru usage
  * 
  * @param bool $max
  * @param integer $round_to
  * @return mixed
  */
  function get_mem_use( $max = true, $round_to = 2 )
  {
      if( function_exists('memory_get_peak_usage') && $max ) {
          return round( (memory_get_peak_usage(true)) / 1048576, $round_to );
      } else if( function_exists('memory_get_usage') && !$max ) {
          return round( memory_get_usage()/1048576, $round_to );
      } 
      
      return 0;
  }
  
// ------------------------------------------------------------------------------
  /**
  * Create random file name
  * 
  * @param string $extension
  */
  function generate_file_name($extension)
  {
      return time() . substr( md5(microtime()), 0, rand(5, 12) ) . $extension;
  } 

// ------------------------------------------------------------------------------
  /**
  * Получение запещённых функция в PHP
  */
  function get_disabled_functions()
  {
      $_ = explode(',', ini_get('disable_functions'));
      $PHP_DISABLED_FUNCTIONS = [];

      foreach($_ as $key=>$val) {
          $func = trim($val);
          if( !empty($func) ) {
              $PHP_DISABLED_FUNCTIONS[] = $val;
          }
      }
      
      unset($_, $key, $val, $func);
      
      return $PHP_DISABLED_FUNCTIONS;
  }
  
// ------------------------------------------------------------------------------
  /**
  * Write packed content to file with gz compression
  * 
  * @param string $filename
  * @param string $content
  * @param integer $compression_level
  */
  function file_put_gz_content($filename, $content, $compression_level = 5)
  {
      if( !empty($filename) && is_scalar($filename) && is_scalar($content) ) {
          return file_put_contents( 
              $filename, 
              gzcompress(
                  (string)$content, 
                  (int)$compression_level
              )
          );
      }
      
      return false;
  }
// ------------------------------------------------------------------------------
  /**
  * Read and unpack gz file
  * 
  * @param string $filename
  */
  function file_get_gz_content($filename)
  {
      if( !empty($filename) && is_file($filename) ) {
          $_ = file_get_contents($filename);
          return ( $_ != '' ) ? gzuncompress( $_ ) : '';
      }
      
      return false;
  }
// ------------------------------------------------------------------------------
  function wasp_microtime()
  {
      return round( microtime(true) * 1000 );
  }
// ------------------------------------------------------------------------------
  function get_file_extension($filename)
  {
      return substr( strrchr($filename, '.'), 1 );
  }
// ------------------------------------------------------------------------------
  function is_image($filepath)
  {
      if( is_file($filepath) && function_exists('getimagesize') ) {
          $img = getimagesize($filepath);

          if( !empty($img['mime']) ) {
              return true;
          }
      }
      
      return false;
  }
// ------------------------------------------------------------------------------
  function get_image_extension($filepath)
  {
      if( is_file($filepath) && function_exists('getimagesize') ) {
          $img = getimagesize($filepath);

          switch($img['mime']) {
              case 'image/jpeg': return 'jpg';
              case 'image/gif':  return 'gif';
              case 'image/png':  return 'png';
              case 'image/x-ms-bmp': return 'bmp';
              default: return '';
          }
      }
      
      return '';
  }
  
// ------------------------------------------------------------------------------

  function rating_stars( $rating )
  {
      $_      = '';
      $rating = round( floatval($rating) );
      
      if( empty($rating) ) return '-';
      
      while( $rating > 0 ) {
          $_ .= '&#9733;';
          $rating--;
      }
      
      return $_;
  }
  
// ------------------------------------------------------------------------------
  /**
  * Функция для упрощенного получения языковой строки
  * 
  * @param string $key
  * @param array $values
  * @return string
  */
  function l( $key, array $values = [] )
  {
      return i18n()->get( $key, $values );
  }
  
// ------------------------------------------------------------------------------

  function ufl( $key, array $values = [] )
  {
      return wasp_ucfirst( l($key, $values) );
  }
  
// ------------------------------------------------------------------------------

  if( !function_exists('studly_case') ) {
      function studly_case( $value )
      {
          return str_replace(' ', '', ucwords( str_replace(['-', '_'], ' ', $value) ));
      }
  }

// ------------------------------------------------------------------------------

  if( !function_exists('camel_case') ) {
      function camel_case( $value )
      {
          return lcfirst( studly_case($value) );
      }
  }
// ------------------------------------------------------------------------------
  if( !function_exists('snake_case') ) {
      function snake_case($value, $delimiter = '_', $convert_spaces = false)
      {
          static $cache;

          if( empty($cache) ) $cache = [];

          $key = $value . $delimiter;
          
          if( isset( $cache[ $key ] ) ) return $cache[ $key ];
          
          if( !ctype_lower($value) ) {
              $value = !$convert_spaces ? $value = preg_replace('/\s+/', '', $value)
                                        : str_replace(' ', $delimiter, $value);
                
              $value = strtolower(
                  preg_replace('/(.)(?=[A-Z])/', '$1' . $delimiter, $value)
              );
          }
            
          return $cache[ $key ] = $value;
      }
  }
}
