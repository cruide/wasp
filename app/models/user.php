<?php namespace App\Models;

use App\Models\UsersContact;

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
    
    public function contacts()
    {
        return  $this->hasMany('\\App\\Models\\UsersContact');
    }
    
    public function emails()
    {
        return $this->hasMany('\\App\\Models\\UsersContact')
                    ->where('type', '=', UsersContact::TYPE_EMAIL);
    }
    
}