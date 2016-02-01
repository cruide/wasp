<?php namespace App\Models;

class UsersSession extends \Wasp\Model
{
    public function user()
    {
        return $this->belongsTo('\\App\\Models\\User');
    }
}