<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

  class Log extends \stdClass
  {
     protected $_logs;
     protected static $_instance;
// -------------------------------------------------------------------------------
     public function __construct()
     {
         $this->_logs   = [];
         $this->_writed = false;
     }
// -------------------------------------------------------------------------------
     /**
     * Добавление log-записи
     * 
     * @param string $method
     * @param string $message
     */
     public function add($message, $error = false)
     {
         if( !empty($message) ) {
             $this->_logs[] = [
                 'time'  => date('Y-m-d H:i:s'),
                 'msg'   => $message,
                 'error' => $error,
             ];
         }
         
         return $this;
     }
// -------------------------------------------------------------------------------
     /**
     * Запись лога в файл
     * 
     */
     public function write()
     {
         $_tmp = '';

         if( !is_dir(LOGS_DIR) ) wasp_mkdir(LOGS_DIR, 0775, true);
         
         if( !empty($this->_logs) ) {
             foreach($this->_logs as $key=>$val) {
                 $_tmp .= "[{$val['time']}] {$val['msg']}\n";
             }

             if( is_file(LOGS_DIR . DIR_SEP . 'app_log') ) {
                 if( filesize(LOGS_DIR . DIR_SEP . 'app_log') >= 614400 ) {
                     $logfile_out = LOGS_DIR . DIR_SEP . 'app-' . date('Ymd');
                     
                     $j = 1;
                     while(true) {
                         if( !is_file($logfile_out . "-{$j}.log.gz") ) {
                             $logfile_out .= "-{$j}.log.gz";
                             break;
                         }
                         $j++;
                     }
                     
                     gz_file_pack(LOGS_DIR . DIR_SEP . 'app_log', $logfile_out);
                     @unlink(LOGS_DIR . DIR_SEP . 'app_log');
                 }
                 
                 $_tmp .= file_get_contents( LOGS_DIR . DIR_SEP . 'app_log' );
                 file_put_contents( LOGS_DIR . DIR_SEP . 'app_log', $_tmp );

             } else {
                 @file_put_contents( LOGS_DIR . DIR_SEP . 'app_log', $_tmp );
             }
             
             $this->_logs = [];
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
// -------------------------------------------------------------------------------
  }
  


