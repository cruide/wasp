<?php namespace \Wasp\Form\Decorators;

use Wasp\Form\Decorators\Decorator;

class Textarea extends Decorator
{
    public function __construct($name, $value)
    {
        $this->tag   = 'textarea';
        $this->name  = $name;
        $this->value = $value;
    }
}
  

