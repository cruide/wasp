<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

  class TempException extends \Wasp\Exception {}

  class Temp extends \stdClass
  {
      protected $_name, $_dir, $_content;
// ------------------------------------------------------------------------------
      /**
      * Constructor
      * 
      * @param string $name
      * @return Temp
      */
      public function __construct( $name, $directory = null )
      {
          if( empty($name) ) {
              throw new TempException( 
                  'Temp::__construct - Undefined temp name' 
              );
          }
          
          $this->_dir  = ( !empty($directory) && is_dir($directory) ) 
                             ? path_correct($directory, true) 
                                 : path_correct(TEMP_DIR, true);
                             
          $this->_name = strtolower($name);
      }
// ------------------------------------------------------------------------------
      public function setPath( $dir )
      {
          if( is_dir($dir) ) {
              $this->_dir = path_correct($dir, true);
          }
          
          return $this;
      }
// ------------------------------------------------------------------------------
      /**
      * Set temp content
      * 
      * @param mixed $content
      * @return Temp
      */
      public function setContent( $content )
      {
          if( is_scalar($content) || is_array($content) ) {
              $this->_content = $content;
          }
          
          return $this;
      }
// ------------------------------------------------------------------------------
      /**
      * Get content
      * 
      */
      public function getContent()
      {
          return $this->_content;
      }
// ------------------------------------------------------------------------------
      /**
      * Write serialized content to file
      * 
      */
      public function write()
      {
          if( $this->_content == '' ) return false;
          if( !file_put_gz_content( $this->_dir . $this->_name, serialize($this->_content) ) ) {
              throw new TempException( 
                  'Temp::write - Could not write a temporary file. Check the correctness of the path.' 
              );
              return false;
          }
          
          return true;
      }
// ------------------------------------------------------------------------------
      public function read()
      {
		  if( is_file($this->_dir . $this->_name) && is_readable($this->_dir . $this->_name) ) {
			  $this->_content = unserialize( file_get_gz_content($this->_dir . $this->_name) );
		  }
		  
		  return $this->_content;
      }
// ------------------------------------------------------------------------------
      /**
      * Delete temp file
      * 
      */
      public function delete()
      {
          if( is_file($this->_dir . $this->_name) ) {
              @unlink($this->_dir . $this->_name);
          }
      }
  }
