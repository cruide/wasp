<?php namespace App\Models;

class UsersGroup extends \Wasp\Model
{
    public function users()
    {
        return $this->hasMany('\\App\\Models\\User');
    }
}