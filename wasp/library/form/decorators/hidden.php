<?php namespace \Wasp\Form\Decorators;

use Wasp\Form\Decorators\Decorator;

class Hidden extends Decorator
{
    public function __construct($name, $value)
    {
        $this->type  = 'hidden';
        $this->name  = $name;
        $this->value = $value;
    }
}
  

