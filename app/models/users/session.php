<?php namespace App\Models\Users;

class Session extends \Wasp\Model
{
    protected $table = 'users_sessions';

    public function user()
    {
        return $this->belongsTo('\\App\\Models\\Users\\User');
    }
}