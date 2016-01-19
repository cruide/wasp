<?php namespace App\Models;

class UsersAuth extends \Wasp\Model
{
    const TYPE_EMAIL    = 'EMAIL';
    const TYPE_PHONE    = 'PHONE';
    const TYPE_NICKNAME = 'NICKNAME';

    public function user()
    {
        return $this->belongsTo('\\App\\Models\\User');
    }
    
    public function session()
    {
        return $this->belongsTo('\\App\\Models\\UsersSession', 'user_id', 'user_id');
    }
}