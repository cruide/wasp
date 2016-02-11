<?php namespace \Wasp\Form\Decorators;

use Wasp\Form\Decorators\Decorator;

class Password extends Decorator
{
    public function __construct($name, $value)
    {
        $this->type = 'password';
        $this->name = $name;
        $this->val($value);
    }
}
  

