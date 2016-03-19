<?php namespace App\Models\Users;

class Profile extends \Wasp\Model
{
    protected $table = 'user_profiles';

    public function user()
    {
        return $this->belongsTo('\\App\\Models\\Users\\User');
    }
}