<?php namespace Wasp\Form;

use Wasp\Form\Decorators\Decorator;

abstract class Builder
{
    protected $post = [];
    protected $rules;
    protected $items;
    
    public function __construct( $items = null )
    {
        if( method_exists($this, '_prepare') ) {
            $this->_prepare();
        }
        
        $this->items = $this->rules = [];
        
        class_alias('\\Wasp\\Form\\Builder', '\\Form'); 
    }
    
    public function setItem( Decorator $item, $rules )
    {
        $name = $item->name;
        
        if( !empty($name) ) {
            $this->items[ $name ] = $item;
            $this->rules[ $name ] = $rules;
        }
        
        return $this;
    }
    
    public function getItem( $name )
    {
        return array_key_isset($name, $this->items) ? $this->items[ $name ] : null; 
    }
    
    public function makeItem( $name, $value = null )
    {
        if( array_key_isset($name, $this->items) ) {
            $this->items[ $name ]->val( $value );
            return $this->items[ $name ]->make();
        }
        
        return '';
    }
}