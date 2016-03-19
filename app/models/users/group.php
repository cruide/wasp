<?php namespace App\Models\Users;

class Group extends \Wasp\Model
{
    protected $table = 'user_groups';

    public function users()
    {
        return $this->hasMany('\\App\\Models\\Users\\User');
    }
}