<?php
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

if( !defined('CORE_PATH') ) {
    define('CORE_PATH', __DIR__);
}

if( !is_file(ROOT . DIR_SEP . '.htaccess') ) {
    file_put_contents(
        ROOT . DIR_SEP . '.htaccess', 
        base64_decode(
            'QWRkRGVmYXVsdENoYXJzZXQgdXRmLTgKRGlyZWN0b3J5SW5kZXggaW5kZXgucGhw' .
            'Cgo8SWZNb2R1bGUgbW9kX2F1dG9pbmRleC5jPgogICAgT3B0aW9ucyAtSW5kZXhl' .
            'cwo8L0lmTW9kdWxlPgoKPElmTW9kdWxlIG1vZF9yZXdyaXRlLmM+CiAgICBPcHRp' .
            'b25zICtGb2xsb3dTeW1saW5rcwogICAgUmV3cml0ZUVuZ2luZSBPbgogICAgUmV3' .
            'cml0ZUJhc2UgLwogICAgUmV3cml0ZUNvbmQgJXtSRVFVRVNUX0ZJTEVOQU1FfSAh' .
            'LWYKICAgIFJld3JpdGVSdWxlIC4qIGluZGV4LnBocCBbTkMsTF0KPC9JZk1vZHVs' .
            'ZT4KCjxJZk1vZHVsZSBtb2RfbWltZS5jPgogICAgQWRkQ2hhcnNldCB1dGYtOCAu' .
            'YXRvbSAuY3NzIC5qcyAuanNvbiAucnNzIC52dHQgLndlYmFwcCAueG1sCjwvSWZN' .
            'b2R1bGU+Cgo8RmlsZXMgfiAiXC4odG1wfGxvZ3xpbml8ZGJ8dHBsfHBoYXIpJCI+' .
            'CmRlbnkgZnJvbSBhbGwKPC9GaWxlcz4K'
        )
    );
}

$_REQUEST_URI = preg_replace( '/^\/\?/', '/', $_SERVER['REQUEST_URI']);
$_REQUEST_URI = preg_replace( '/^\/+/', '/', $_REQUEST_URI);

require(CORE_PATH . DIR_SEP . 'constants.php');
require(CORE_PATH . DIR_SEP . 'functions.php');
require(CORE_PATH . DIR_SEP . 'validators.php');

require(CORE_LIBRARY_DIR . DIR_SEP . 'exception.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'stdobject.php');

$app = cfg('config')->application;

if( isset($app->develop_mode) && $app->develop_mode == true ) {
    define('DEVELOP_MODE', true);
} else {
    define('DEVELOP_MODE', false);
}

unset($app);

require(CORE_LIBRARY_DIR . DIR_SEP . 'input.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'log.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'session.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'cookie.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'temp.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'router.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'native.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'app.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'theme.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'controller.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'library.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'hook.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'wcurl.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'wxml.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'i18n.php');
require(CORE_LIBRARY_DIR . DIR_SEP . 'validator.php');

if( is_dir(LIBRARY_DIR) && is_file(LIBRARY_DIR . DIR_SEP . 'functions.php') ) {
    require(LIBRARY_DIR . DIR_SEP . 'functions.php');
}

require_once(CORE_LIBRARY_DIR . DIR_SEP . 'vendor' . DIR_SEP . 'autoload.php');
spl_autoload_register('wasp_autoloader');

if( !headers_sent() ) header('X-Based-On: ' . FRAMEWORK);

if( !is_dir(CONTENT_DIR) )       { wasp_mkdir(CONTENT_DIR);}
if( !is_dir(TEMP_DIR) )          { wasp_mkdir(TEMP_DIR);}
if( !is_dir(LOGS_DIR) )          { wasp_mkdir(LOGS_DIR);}

$DisabledFunctions = get_disabled_functions();

if( !in_array('ini_set', $DisabledFunctions) ) {
    $php_cfg  = cfg('config')->php;

    if( $php_cfg->count() > 0 ) {
        foreach($php_cfg->toArray() as $key=>$val) {
            $key = str_replace( '__', '.', $key);
            @ini_set($key, $val);
        }
        
        unset($key, $val);
    }
    
    unset($php_cfg);
}

unset($DisabledFunctions);

$config = cfg('dbase');

if( $config->count() > 0 ) {
    $capsule = new \Illuminate\Database\Capsule\Manager();
    
    foreach($config->toArray() as $key=>$val) {
        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $val->host,
            'database'  => $val->database,
            'username'  => $val->username,
            'password'  => $val->password,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => $val->prefix,
        ], strtolower($key));
    }

    $capsule->setAsGlobal();
    $capsule->bootEloquent();
    $capsule->setEventDispatcher(
        new \Illuminate\Events\Dispatcher(
            new \Illuminate\Container\Container()
        )
    );
    
    
    class_alias('\\Illuminate\\Database\\Capsule\\Manager', '\\Wasp\\DB');
    class_alias('\\Illuminate\\Database\\Schema\\Builder', '\\Wasp\\Schema');

    require(CORE_LIBRARY_DIR . DIR_SEP . 'model.php');
}

\Wasp\App::mySelf()->execute();