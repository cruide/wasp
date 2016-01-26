<?php namespace \Wasp\Form\Decorators;

use Wasp\Validator;

abstract class Decorator
{
    protected $_tag          = 'input';
    protected $_properties   = [];
    protected $_do_not_cover = ['input', 'br', 'hr', 'meta', 'img', 'link', 'base'];
    protected $_value        = '';
    
    public function make()
    {
        $_ = "<{$this->_tag}";
        
        foreach($this->_properties as $prop=>$value) {
            $_ .= " {$prop}=\"{$value}\"";
        }
        
        if( in_array($this->_tag, $this->_do_not_cover) ) {
            $_ .= " value=\"{$this->_value}\" />";
        } else {
            $_ .= ">{$this->_value}</{$this->_tag}>";
        }
        
        return $_;
    }
    
    public function val( $value = null )
    {
        if( $value !== null && is_scalar($value) ) {
            $this->_value = $value;
        }
        
        return $this->_value;
    }

    public function tag( $value = null )
    {
        if( $value !== null && is_scalar($value) ) {
            $this->_tag = $value;
        }
        
        return $this->_tag;
    }
    
    public function setAttribute($name, $value)
    {
        if( is_varible_name($name) && $name != 'value' ) {
            $this->_properties[ $name ] = $value;
        }

        return $this;
    }
    
    public function getAttribute($name)
    {
        return ( array_key_isset($name, $this->_properties) ) ? $this->_properties[ $name ] : null;
    }
    
    public function __set($name, $value)
    {
        if( $name == 'value' ) {
            $this->val($value);
        } else if( $name == 'tag' ) {
            $this->tag($value);
        } else {
            $this->setAttribute($name, $value);
        }
    }
    
    public function __get($name)
    {
        if( $name == 'value' ) {
            return $this->val();
            
        } else if( $name == 'tag' ) {
            return $this->_tag;
            
        } else {
            if( array_key_isset($name, $this->_properties) ) {
                return $this->_properties[ $name ];
            }
        }

        return null;
    }
    
    public function __isset($name)
    {
        return array_key_isset($name, $this->_properties);
    }
    
    public function __unset($name)
    {
        unset( $this->_properties[ $name ] );
    }
}
  

