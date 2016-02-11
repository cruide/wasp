<?php namespace App\Models\Users;

class User extends \Wasp\Model
{
    protected $table = 'users';

    public function profile()
    {
        return $this->hasOne('\\App\\Models\\Users\\Profile');
    }
    
    public function session()
    {
        return $this->hasOne('\\App\\Models\\Users\\Session');
    }
    
    public function group()
    {
        return $this->belongsTo('\\App\\Models\\Users\\Group', 'group_id');
    }
    
    public function contacts()
    {
        return  $this->hasMany('\\App\\Models\\Users\\Contact');
    }
    
    public function emails()
    {
        return $this->hasMany('\\App\\Models\\Users\\Contact')
                    ->where('type', '=', UsersContact::TYPE_EMAIL);
    }
    
}