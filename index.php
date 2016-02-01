<?php 
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

@ini_set('date.timezone', 'Europe/Moscow');

define('WASP_START_TIME', microtime( true ));
define('DIR_SEP'        , DIRECTORY_SEPARATOR);
define('ROOT'           , __DIR__);
define('PROTOCOL'       , 'http' . ( (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') );
define('SELF_DOMAIN'    , $_SERVER['HTTP_HOST']);
define('BASE_URL'       , PROTOCOL . '://' . SELF_DOMAIN);

//require_once('wasp.phar');
require_once('wasp' . DIR_SEP . 'bootstrap.php');