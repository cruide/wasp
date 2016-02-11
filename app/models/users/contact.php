<?php namespace App\Models\Users;

class Contact extends \Wasp\Model
{
    const TYPE_EMAIL = 1;
    const TYPE_PHONE = 2;
    const TYPE_SKYPE = 3;
    
    protected $table = 'users_contacts';

    public function user()
    {
        return $this->belongsTo('\\App\\Models\\Users\\User');
    }
    
    public function session()
    {
        return $this->belongsTo('\\App\\Models\\Users\\Session', 'user_id', 'user_id');
    }
}