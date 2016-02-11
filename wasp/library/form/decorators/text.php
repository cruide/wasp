<?php namespace \Wasp\Form\Decorators;

use Wasp\Form\Decorators\Decorator;

class Text extends Decorator
{
    public function __construct($name, $value)
    {
        $this->setAttribute('type', 'text')
             ->setAttribute('name', $name)
             ->setValue($value);
    }
}
  

