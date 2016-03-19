<?php namespace App\Controllers;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

class Js extends \Wasp\Controller
{
    public function anyDefault()
    {
        return $this->javascript('js/default.tpl');
    }
}
