<?php namespace Wasp;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

class Validator extends \stdClass
{
    protected $values   = [];
    protected $rules    = [];
    protected $messages = [];

    protected $errors   = [
        'require' => 'Обязательно должно быть заполнено',
        'fail'    => 'Поле не прошло проверку',
    ];
// -------------------------------------------------------------------------------------
    public function __construct( array $values, array $rules )
    {
        $this->values = $values;
        
        foreach($rules as $key=>$val) {
            if( is_array($val) || (is_callable($val) && $val instanceof Closure) ) {
                $this->rules[ $key ] = $val;
            } else {
                $this->rules[ $key ] = $this->_parse_rules($val);
            }
        }
    }
// -------------------------------------------------------------------------------------
    public function __destruct()
    {
        unset($this->errors, $this->messages, $this->rules, $this->values);
    }
// -------------------------------------------------------------------------------------
    /**
    * Check all data
    * 
    * @return bool
    */
    public function checkAll()
    {
        $_ = true;

        foreach($this->rules as $key=>$val) {
            if( array_key_isset('required', $val) 
             && $val['required'] == true 
             && !array_key_isset($key, $this->values) )
            {
                if( !isset($this->messages[ $key ]) ) {
                    $this->messages[ $key ] = [];
                }
                
                $this->messages[ $key ][] = 'Required field';
                $_ = false;
                
            } else if( array_key_isset($key, $this->values) ) {
                if( !$this->_check($key, $this->values[ $key ]) ) {
                    $_ = false;
                }
            }
        }
        
        return $_;
    }
// -------------------------------------------------------------------------------------
    /**
    * Get error messages
    * 
    * @return []
    */
    public function getMessages()
    {
        return $this->messages;
    }
// -------------------------------------------------------------------------------------
    /**
    * Check one rule
    * 
    * @param string $key
    * @return bool
    */
    public function checkOne($key)
    {
        if( array_key_isset($key, $this->values) ) {
            return $this->_check($key, $this->values[$key]);
        }
        
        return false;
    }
// -------------------------------------------------------------------------------------
    /**
    * Protectedm method for check one rule
    * 
    * @param string $key
    * @param mixed $value
    */
    protected function _check($key, $value)
    {
        if( !array_key_isset($key, $this->rules) ) {
            return false;
        }
        
        if( !is_scalar($value) ) {
            return false;
        }
        
        $this->messages[ $key ] = [];
        $rules                  = $this->rules[ $key ];

        if( is_callable($rules) ) {
            return $rules($key, $value);
        }
        
        if( isset($rules['required']) && $rules['required'] == true && $value == '' ) {
            $this->messages[ $key ][] = 'Данное поле обязательно для заполнения';
            return false;
        }
        
        if( (!isset($rules['required']) || $rules['required'] == false) && $value == '' ) {
            return true;
        }
        
        if( isset($rules['regexp']) && !empty($rules['regexp']) && !preg_match($rules['regexp'], $value, $tmp) ) {
            $this->messages[ $key ][] = 'Поле не прошло проверку';
            
        } else if ( isset($rules['validator'])  ) {
            $validator = 'is_' . $rules['validator'];
            
            if( !$validator( $value ) ) {
                $this->messages[ $key ][] = 'Поле не прошло проверку';
            }
        }
        
        if( isset($rules['maxlen']) && wasp_strlen($value) > intval($rules['maxlen']) ) {
            $this->messages[ $key ][] = 'Значение превышает ' . intval($rules['maxlen']) . ' символов';
        }
        
        if( isset($rules['minlen']) && wasp_strlen($value) < intval($rules['minlen']) ) {
            $this->messages[ $key ][] = 'Значение должно быть больше или равно ' . intval($rules['minlen']) . ' символов';
        }
        
        if( isset($rules['max']) ) {
            if( !is_numeric($value) ) {
                $this->messages[ $key ][] = 'Значение должно быть числом';
            } else {
                if( $value > $rules['max'] ) {
                    $this->messages[ $key ][] = 'Превышает допустимое значение';
                }
            }
        }

        if( isset($rules['min']) ) {
            if( !is_numeric($value) ) {
                $this->messages[ $key ][] = 'Значение должно быть числом';
            } else {
                if( $value < $rules['min'] ) {
                    $this->messages[ $key ][] = 'Менее допустимого значения';
                }
            }
        }
        
        if( array_count($this->messages[$key]) > 0 ) {
            return false;
        }
        
        return true;
    }
// -------------------------------------------------------------------------------------
    /**
    * Rule parser
    * 
    * @param mixed $rules
    */
    protected function _parse_rules( $rules )
    {
        if( empty($rules) ) {
            return false;
        }
        
        $rules = str_replace('\|', '~~~', $rules);
        $_     = [];
        $data  = explode('|', $rules);
        
        foreach($data as $key=>$val) {
            $value = strtolower($val);
            
            if( $value == 'required' ) {
                $_['required'] = true;
            
            } else if( preg_match('/^regexp:/', $value) ) {
                $_['regexp'] = str_replace('~~~', '\|', str_replace('regexp:', '', $value));
                
            } else if( preg_match('/[\:]+/', $value) ) {
                list($k, $v) = explode(':', $value);
                switch($k) {
                    case 'min'   : $_['min']    = intval($v); break;
                    case 'max'   : $_['max']    = intval($v); break;
                    case 'minlen': $_['minlen'] = intval($v); break;
                    case 'maxlen': $_['maxlen'] = intval($v); break;
                }
            } else if( function_exists('is_' . $value) ) {
                $_['validator'] = $value;
                
            }
        }
        
        return $_;
    }
}