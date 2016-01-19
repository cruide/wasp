<?php if( !defined('__VALIDATORS__') ) { define('__VALIDATORS__', true);
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

    function is_ipaddress($str)
    {
        return filter_var($str, FILTER_VALIDATE_IP);
    }
// -------------------------------------------------------------------------------------
    function is_email($str)
    {
        return filter_var($str, FILTER_VALIDATE_EMAIL);
    }
// -------------------------------------------------------------------------------------
    function is_url($str)
    {
        return filter_var($str, FILTER_VALIDATE_URL);
    }
// -------------------------------------------------------------------------------------
    function is_alpha($str)
    {
        if( !isset($str) || !is_scalar($str) || !preg_match('/^[a-zA-Zа-яА-ЯЁё]+$/u', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_action_name($str)
    {
        if( empty($str) || !is_scalar($str) || preg_match('/^[0-9_\-]/', (string)$str) || !preg_match('/^[a-z0-9_\-]+$/i', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_varible_name($str)
    {
        if( !isset($str) 
         || !is_scalar($str) 
         || !preg_match('/^[a-z0-9\_]+$/i', (string)$str) 
         || preg_match('/^[0-9]/', (string)$str) ) 
        {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_cookie_varible_name($str)
    {
        if( !isset($str) 
         || !is_scalar($str) 
         || !preg_match('/^[a-z0-9\_\-]+$/i', (string)$str) 
         || preg_match('/^[0-9\-]/', (string)$str) ) 
        {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_date($str)
    {
        if( empty($str) || !is_scalar($str) || !preg_match('%^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9]{2}+$%', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_date_us($str)
    {
        if( empty($str) || !is_scalar($str) || !preg_match('%^(19|20)[0-9]{2}[-/](0[1-9]|1[012])[-/](0[1-9]|[12][0-9]|3[01])$%', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_sql_date($str)
    {
        if( empty($str) || !is_scalar($str) || !preg_match('%^(19|20)[0-9]{2}[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])$%', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_time($str)
    {
        if( empty($str) || !is_scalar($str) || !preg_match('/^(2[0-3]|[0-1][0-9]):[0-5][0-9]:[0-5][0-9]+$/', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_time_m($str)
    {
        if( empty($str) || !is_scalar($str) || !preg_match('/^(2[0-3]|[0-1][0-9]):[0-5][0-9]$/', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_alphanum($str)
    {
        if( !isset($str) || !is_scalar($str) || !preg_match('/^[a-zA-Zа-яА-ЯЁё0-9]+$/u', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_name($str)
    {
        if( !isset($str) || !is_scalar($str) || !preg_match('/^[a-zA-Zа-яА-ЯЁё0-9\s\']+$/u', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_latin_only($str)
    {
        if( !isset($str) || !is_scalar($str) || !preg_match('/^[a-z]+$/i', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_plaintext($str)
    {
        if( !isset($str) || !is_scalar($str) || !preg_match('/^[а-яА-ЯЁё\.\,\'\"\&\w\d\s\[\]\{\}\^\%\$\#\\\!\?\-\=\|\_\*\+\(\)\:\;\/\@\“\”\»\«\`\~]+\z$/u', (string)$str) ) {
            return false;
        }

        return true;
    }
// -------------------------------------------------------------------------------------
    function is_words($str)
    {
        if( !isset($str) || !is_scalar($str) || !preg_match('/^[\w\s]+$/iu', (string)$str) ) {
            return false;
        }

        return true;
    }
    
// -------------------------------------------------------------------------------------
    function is_text($str)
    {
        if( !isset($str) || !is_scalar($str) || !preg_match('/^[\w\s\,\.\"\'\-\=\+\/\?\!\:\;\&\#\@\%\*\(\)]+$/iu', (string)$str) ) {
            return false;
        }

        return true;
    }
    
    function is_match($value, $validator)
    {
        if( function_exists($validator) ) {
            return $validator( $value );
        } else {
            wasp_error('Incorrect validator `' . $validator . '`');
        }
    }
}