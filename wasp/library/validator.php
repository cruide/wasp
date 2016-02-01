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
        'required' => 'validator_required',
        'minlen'   => 'validator_minlen',
        'maxlen'   => 'validator_maxlen',
        'regexp'   => 'validator_regexp',
        'min'      => 'validator_min',
        'max'      => 'validator_max',
        'numeric'  => 'validator_numeric',
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
                
                $this->messages[ $key ][] = ufl( $this->errors['required'] );
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
        
        $rules = $this->rules[ $key ];

        if( is_callable($rules) ) {
            return $rules($key, $value);
        }
        
        if( isset($rules['required']) && $rules['required'] == true && $value == '' ) {
            $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['required'] );
            return false;
        }
        
        if( (!isset($rules['required']) || $rules['required'] == false) && $value == '' ) {
            return true;
        }
        
        if( isset($rules['regexp']) && !empty($rules['regexp']) && !preg_match($rules['regexp'], $value, $tmp) ) {
            $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['regexp'] );
            return false;
            
        } else if ( isset($rules['validator'])  ) {
            $validator = 'is_' . $rules['validator'];
            
            if( !$validator( $value ) ) {
                $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['regexp'] );
            }
        }
        
        if( isset($rules['maxlen']) && wasp_strlen($value) > intval($rules['maxlen']) ) {
            $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['maxlen'], ['maxlen' => intval($rules['maxlen'])] );
            return false;
        }
        
        if( isset($rules['minlen']) && wasp_strlen($value) < intval($rules['minlen']) ) {
            $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['minlen'], ['minlen' => intval($rules['minlen'])] );;
            return false;
        }
        
        if( isset($rules['max']) ) {
            if( !is_numeric($value) ) {
                $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['numeric'] );
                return false;
            } else {
                if( $value > $rules['max'] ) {
                    $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['max'], ['max' => intval($rules['max'])] );
                    return false;
                }
            }
        }

        if( isset($rules['min']) ) {
            if( !is_numeric($value) ) {
                $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['numeric'] );
                return false;
            } else {
                if( $value < $rules['min'] ) {
                    $this->messages[ $key ] = ( !empty($rules['message']) ) ? $rules['message'] : ufl( $this->errors['min'], ['min' => intval($rules['min'])] );
                    return false;
                }
            }
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
                
            } else if( preg_match('/^message:/', $value) ) {
                $_['message'] = str_replace('message:', '', $value);
                
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