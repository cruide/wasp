<?php namespace App\Models\Users;

class Profile extends \Wasp\Model
{
    protected $table = 'users_profiles';

    public function user()
    {
        return $this->belongsTo('\\App\\Models\\Users\\User');
    }
}