<?php namespace App\Models;

use App\Models\UsersAuth;

class User extends \Wasp\Model
{
    public function profile()
    {
        return $this->hasOne('\\App\\Models\\UsersProfile');
    }
    
    public function session()
    {
        return $this->hasOne('\\App\\Models\\UsersSession');
    }
    
    public function group()
    {
        return $this->belongsTo('\\App\\Models\\UsersGroup', 'group_id');
    }
    
    public function auths()
    {
        return  $this->hasMany('\\App\\Models\\UsersAuth');
    }
    
    public function email()
    {
        return $this->hasMany('\\App\\Models\\UsersAuth')
                    ->where('method', '=', UsersAuth::TYPE_EMAIL)
                    ->first()->value;
    }
    
    public function emails()
    {
        return $this->hasMany('\\App\\Models\\UsersAuth')
                    ->where('method', '=', UsersAuth::TYPE_EMAIL);
    }
    
}