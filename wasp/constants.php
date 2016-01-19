<?php if( !defined('__CONSTANTS__') ) { define('__CONSTANTS__', true);
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

    define('CORE_NAME'         , 'Wasp');
    define('CORE_VERSION'      , '1.4.1');
    define('CORE_VERSION_NAME' , 'preview');
    define('FRAMEWORK'         , CORE_NAME . ' - MVC micro-framework v' . CORE_VERSION);

    define('CORE_LIBRARY_DIR'  , CORE_PATH . DIR_SEP . 'library');
    define('APP_DIR'           , ROOT . DIR_SEP . 'app');
    define('LOGS_DIR'          , ROOT . DIR_SEP . 'logs');
    define('TEMP_DIR'          , ROOT . DIR_SEP . 'tmp');

    define('CONTROLLERS_DIR'   , APP_DIR . DIR_SEP . 'controllers');
    define('SETTINGS_DIR'      , APP_DIR . DIR_SEP . 'settings');
    define('MODELS_DIR'        , APP_DIR . DIR_SEP . 'models');
    define('LIBRARY_DIR'       , APP_DIR . DIR_SEP . 'library');
    define('HOOKS_DIR'         , APP_DIR . DIR_SEP . 'hooks');
    define('LANGUAGE_DIR'      , APP_DIR . DIR_SEP . 'i18n');
    define('FORMS_DIR'         , APP_DIR . DIR_SEP . 'forms');
    
    define('CONTENT_URL'       , BASE_URL . '/content');
    define('CONTENT_DIR'       , ROOT . DIR_SEP . 'content');
    define('THEMES_URL'        , BASE_URL . '/themes');
    define('THEMES_DIR'        , ROOT . DIR_SEP . 'themes');
    define('ASSETS_URL'        , BASE_URL . '/assets');
    define('ASSETS_DIR'        , ROOT . DIR_SEP . 'assets');

    define('CONTENT_TYPE_XML'  , 'Content-type: text/xml; charset=utf-8');
    define('CONTENT_TYPE_CSS'  , 'Content-type: text/css; charset=utf-8');
    define('CONTENT_TYPE_HTML' , 'Content-type: text/html; charset=utf-8');
    define('CONTENT_TYPE_PLAIN', 'Content-type: text/plain; charset=utf-8');
    define('CONTENT_TYPE_JS'   , 'Content-type: application/javascript; charset=utf-8');
    define('CONTENT_TYPE_JSON' , 'Content-type: application/json; charset=utf-8');
    define('CONTENT_TYPE_PDF'  , 'Content-type: application/pdf; charset=utf-8');
    
}