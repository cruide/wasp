<?php
  
class Wasp_Smarty_Security extends \Smarty_Security 
{
    public $php_functions = ['isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array', 'time', 'l', 'ufl', 'make_url', 'rating_stars', 'array_count', 'array_key_isset', 'date'];
    public $php_modifiers = ['l', 'ufl', 'rating_stars', 'make_url', 'count', 'date'];
}
