<?php namespace App\Models;

class UsersProfile extends \Wasp\Model
{
    public function user()
    {
        return $this->belongsTo('\\App\\Models\\User');
    }
}