<?php namespace App\Models;

class UsersContact extends \Wasp\Model
{
    const TYPE_EMAIL = 1;
    const TYPE_PHONE = 2;
    const TYPE_SKYPE = 3;

    public function user()
    {
        return $this->belongsTo('\\App\\Models\\User');
    }
    
    public function session()
    {
        return $this->belongsTo('\\App\\Models\\UsersSession', 'user_id', 'user_id');
    }
}