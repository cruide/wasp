<?php namespace App\Models\Users;

class Session extends \Wasp\Model
{
    protected $table = 'user_sessions';

    public function user()
    {
        return $this->belongsTo('\\App\\Models\\Users\\User');
    }

}