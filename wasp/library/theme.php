<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

  class Theme extends \stdClass
  {
      protected $_css_list;
      protected $_js_list;
      protected $_theme_name;
      protected $_module_name;
      protected $_layout;
      protected $_config;
      protected $render;
      protected $_headers;
      protected $_assigns;
      protected static $_instance;
// -------------------------------------------------------------------------------------
      public static function mySelf()
      {
          if( null === self::$_instance ) {
              self::$_instance = new self();
          }
 
          return self::$_instance;
      }
// -------------------------------------------------------------------------------------
      public function __construct()
      {
      	  $this->_config     = cfg('config')->interface;
          $this->_theme_name = $this->_config->theme;
          $this->_layout     = $this->_config->layout;
          $this->_headers    = [];
          $this->_assigns    = [];
          
          if( empty($this->_theme_name) ) {
              $this->_theme_name = 'default';
          }
          
          if( empty($this->_layout) ) {
              $this->_layout = 'layout';
          }
          
          $this->setHeader( CONTENT_TYPE_HTML );
          $this->enable();
      }
      
// -------------------------------------------------------------------------------------
      /**
      * Set headers
      * 
      * @param string $header
      * @return Theme
      */
      public function setHeader($header)
      {
          /* if $header is array */
          if( array_count($header) > 0 ) {
              foreach($header as $val) {
                  if( !empty($val) && is_string($val) && !in_array($val, $this->_headers) ) {
                      if( preg_match('/^Content\-type/i', $val) ) {
                          $this->_contentTypeClear();
                      }
                      
                      $this->_headers[] = $val;
                  }
              }
              
              return $this;
          }
          
          /* if $header is string */
          if( !empty($header) && is_string($header) && !in_array($header, $this->_headers) ) {
              if( preg_match('/^Content\-type/i', $header) ) {
                  $this->_contentTypeClear();
              }

              $this->_headers[] = $header;
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      protected function _contentTypeClear()
      {
          $tmp = [];

          foreach($this->_headers as $val) {
              if( !preg_match('/^Content\-type/i', $val) ) {
                  $tmp[] = $val;
              }
          }
          
          $this->_headers = $tmp;
      }
// -------------------------------------------------------------------------------------
      public function setLayout($name)
      {
          if( !empty($name) && is_file($this->getThemePath() . DIR_SEP . $name) ) {
              $this->_layout = $name;
          }
      }
// -------------------------------------------------------------------------------------
      public function useExternalCss($url)
      {
          if( !is_array($this->_css_list) ) {
              $this->_css_list = [];
          }
          
          if( is_url_exists($url) ) {
              $this->_css_list[] = $url;
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function useThemeCss($css_filename)
      {
          $css_file_path = path_correct( $this->getThemePath() . DIR_SEP . 'css' . DIR_SEP . $css_filename );

          if( is_file($css_file_path) ) {
              $this->_css_list[] = $css_filename;
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      /**
      * Добавление внешнего JS скрипта
      *   
      * @param string $url
      * @param bool $head - в заголовке <HEAD> или перед </BODY>
      * @return Theme
      */
      public function useExternalJs($url, $head = true)
      {
          if( !is_array($this->_js_list) ) {
              $this->_js_list = [];
          }
          
          $position = ($head === false) ? 'footer' : 'header';
          
          if( !is_array($this->_js_list[ $position ]) ) {
              $this->_js_list[ $position ] = [];
          }
          
          if( is_url_exists($url) ) {
              $this->_js_list[ $position ][] = $url;
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      /**
      * Добавление JS скрипта
      *   
      * @param string $js_filename
      * @param bool $head - в заголовке <HEAD> или перед </BODY>
      * @return Theme
      */
      public function useThemeJs($js_filename, $head = true)
      {
          $js_file_path = path_correct( $this->getThemePath() . DIR_SEP . 'js' . DIR_SEP . $js_filename );

          if( is_file($js_file_path) ) {
              $position = ($head === false) ? 'body' : 'head';
              
              if( !is_array($this->_js_list[ $position ]) ) {
                  $this->_js_list[ $position ] = [];
              }
              
              $this->_js_list[ $position ][] = $js_filename;
          }
          
          return $this;
      }
// -------------------------------------------------------------------------------------
      public function setTheme($theme_name)
      {
          if( !empty($theme_name) && is_dir(THEMES_DIR . DIR_SEP . $theme_name) ) {
              $this->_theme_name = $theme_name;
              
              $view = App::$controller->getUi();

              if( null !== $view && $view instanceof Native ) {
                  if( !is_dir(TEMP_DIR . DIR_SEP . $theme_name . DIR_SEP . 'views') ) {
                      wasp_mkdir( TEMP_DIR . DIR_SEP . $theme_name . DIR_SEP . 'views' );
                  }
                  
                  $view->setTemplateDir( THEMES_DIR . DIR_SEP . $theme_name . DIR_SEP . 'views' . DIR_SEP );
			  }
          }
      }
// -------------------------------------------------------------------------------------
      public function disable()
      {
		  $this->render = false;
		  return $this;
      }
// -------------------------------------------------------------------------------------
      public function enable()
      {
		  $this->render = true;
		  return $this;
      }
// -------------------------------------------------------------------------------------
      public function getThemeUrl()
      {
          return THEMES_URL . '/' . $this->_theme_name;
      }
// -------------------------------------------------------------------------------------
      public function getThemeName()
      {
          return $this->_theme_name;
      }
// -------------------------------------------------------------------------------------
      public function getThemePath()
      {
          return THEMES_DIR . DIR_SEP . $this->_theme_name;
      }
// -------------------------------------------------------------------------------------
      public function getAjaxDebugInfo($out, $json = false)
      {
          if( is_ajax() ) {
              if( $json === true ) {
                  $_ = new \stdClass();
                  $_->exectime = round( microtime(true) - WASP_START_TIME, 3);
                  $_->mem      = round( (memory_get_peak_usage(true)) / 1048576, 2);
                  
                  return json_encode($_);
              }
              
              return $out . "\n<!-- Exec: " . round( microtime(true) - WASP_START_TIME, 3) . 
                            's, Mem: ' . round( (memory_get_peak_usage(true)) / 1048576, 2) . 'Mb';
          }
          
          return '';
      }
// -------------------------------------------------------------------------------------
      public function getDebugInfo($out) 
      {
          $SQL = '';
          
          if( DEVELOP_MODE ) {
              $SQL = ', SQL: ' . count( \Wasp\DB::getQueryLog() );
          }
          
          return str_replace(
              '<!-- DEBUG-INFO -->', 
              'Exec: ' . round( microtime(true) - WASP_START_TIME, 3) .
              's, Mem: ' . round( (memory_get_peak_usage(true)) / 1048576, 2) . 'Mb ' .
              $SQL, $out
          );
      }
// -------------------------------------------------------------------------------------
      public function assign($key, $value)
      {
          $this->_assigns[ $key ] = $value;
          
          return $this;
      }
      
      public function getAssigns()
      {
          return $this->_assigns;
      }
// -------------------------------------------------------------------------------------
      public function display($content)
      {
          if( !$this->render ) {

              if( !headers_sent() && array_count($this->_headers) > 0 ) {
                  foreach($this->_headers as $key=>$val) {
                      header($val);
                  }
              }

              http_cache_off();

              if( !Cookie::isSaved() ) cookie()->save();
              if( wasp_strlen($content) > 102400 ) @ini_set('zlib.output_compression', 1);

              echo $this->getDebugInfo($content);
              
			  return;
          }

          $templater = new \Smarty();

          $templater->enableSecurity('Wasp_Smarty_Security');
          $templater->setTemplateDir( $this->getThemePath() . DIR_SEP );
        
          $temp_dir = TEMP_DIR . DIR_SEP . 'smarty' . DIR_SEP . $this->getThemeName();
        
          if( !is_dir($temp_dir) ) {
              wasp_mkdir( $temp_dir );
          }
        
          $templater->setCompileDir( $temp_dir . DIR_SEP );
          
          if( array_count($this->_assigns) > 0 ) {
              foreach($this->_assigns as $key=>$val) {
                  $templater->assign($key, $val);
              }
          }
          
          $templater->assign('content', $content);

          if( function_exists('memory_get_peak_usage') ) {
              $templater->assign('max_mem_use', get_mem_use(true));
          } else { 
              $templater->assign('max_mem_use', '-//-');
          }

          $out = $templater->fetch( $this->_layout ) ;
          
          if( !headers_sent() && array_count($this->_headers) > 0 ) {
              foreach($this->_headers as $key=>$val) {
                  header($val);
              }
          }
          
          if( !Cookie::isSaved() ) cookie()->save();
          if( wasp_strlen($out) > 102400 ) ini_set('zlib.output_compression', 1);
          
          unset($templater);
          memory_clear();

          /**
          * Add CSS
          */
          if( array_count($this->_css_list) > 0 ) {
              $_ = "\n\t\t<!-- DYNAMIC CSS -->\n";
              
              foreach($this->_css_list as $key=>$val) {
                  if( preg_match('/^http/is', $val) ) {
                      $_ .= "\t\t<link href=\"{$val}\" rel=\"stylesheet\" type=\"text/css\" />\n";
                  } else {
                      $url = $this->getThemeUrl() . '/css/' . $val;
                      $_ .= "\t\t<link href=\"{$url}\" rel=\"stylesheet\" type=\"text/css\" />\n";
                  }
              }
              
              $out = preg_replace('#\<\/head\>#is', $_ . "</head>\n", $out);
              
              unset($_, $key, $val, $url);
          }

          /**
          * Add JS
          */
          if( array_count($this->_js_list) > 0 ) {
              $info = "\n\t\t<!-- :position DYNAMIC JS -->\n";
              
              foreach($this->_js_list as $pos=>$item) {
                  $_ = str_replace(':position', wasp_strtoupper($pos), "\n\t\t<!-- :position DYNAMIC JS -->\n");
                  
                  if( array_count($item) > 0 ) {
                      foreach($item as $key=>$val) {
                          if( preg_match('/^http/is', $val) ) {
                              $_ .= "\t\t<script type=\"text/javascript\" src=\"{$val}\"></script>\n";
                          } else {
                              $url = $this->getThemeUrl() . '/js/' . $val;
                              $_ .= "\t\t<script type=\"text/javascript\" src=\"{$url}\"></script>\n";
                          }
                      }
                      
                      $out = preg_replace("#\<\/{$pos}\>#is", $_ . "</{$pos}>\n", $out);
                      unset($_, $key, $val, $url);
                  }
              }
              
              unset($pos, $item);
          }
          
          echo $this->getDebugInfo($out);
      }
  }
