<?php namespace Wasp;

class stdObjectException extends \Exception {}

class stdObject extends \stdClass
{
    protected $_classId;
    protected $_properties = [];
    
// -------------------------------------------------------------------------------------
    /**
    * Constructor of class
    * 
    * @param array $_
    * @return stdObject
    */
    public function __construct( $_ = [] )
    {
        $this->_classId = uniqid() . '_' . get_class($this);
        $this->fromArray($_);
    }
// -------------------------------------------------------------------------------------
    public function getClassId()
    {
        return $this->_classId;
    }
// -------------------------------------------------------------------------------------
    /**
    * Set properties from array
    * 
    * @param array $_
    */
    public function fromArray( $_ )
    {
        if( !is_array($_) || count($_) <= 0 ) return false;
        
        foreach($_ as $key=>$val) {
            if( is_string($key) && !preg_match('/^[0-9_]/', $key) && preg_match('/^[a-z0-9_]+$/i', $key) ) {
                $this->_properties[ $key ] = $val;
            } else {
                throw new stdObjectException('Incorrect varible name');
            }
        }
        
        return true;
    }
// -------------------------------------------------------------------------------------
    /**
    * return properties as array
    * 
    */
    public function toArray()
    {
        return $this->_properties;
    }
// -------------------------------------------------------------------------------------
    public function count()
    {
        return count($this->_properties);
    }
// -------------------------------------------------------------------------------------
    public function toJSON()
    {
        return json_encode($this->_properties);
    }
// -------------------------------------------------------------------------------------
    public function __get($name)
    {
        if( array_key_isset($name, $this->_properties) ) {
            $method = studly_case($name);
            $method = "get{$method}Property";

            if( method_exists($this, $method) ) {
                return $this->$method( $name );
            }
            
            return $this->_properties[ $name ];
        }
        
        return null;
    }
// -------------------------------------------------------------------------------------
    public function __set($name, $value)
    {
        if( is_string($name) && is_varible_name($name) ) {
            $method = studly_case($name);
            $method = "set{$method}Property";
            
            if( method_exists($this, $method) ) {
                $this->_properties[ $name ] = $this->$method( $value );
            } else {
                $this->_properties[ $name ] = $value;
            }
        } else {
            throw new stdObjectException( 'Incorrect varible name' );
        }
    }
// -------------------------------------------------------------------------------------
    public function __isset( $name )
    {
        return array_key_isset($name, $this->_properties);
    }
// -------------------------------------------------------------------------------------
    public function __unset($name)
    {
        if( array_key_isset($name, $this->_properties) ) {
            unset( $this->_properties[$name] );
        }
    }
// -------------------------------------------------------------------------------------
    public function __destruct()
    {
        unset($this->_properties, $this->_classId);
        memory_clear();
    }
}
