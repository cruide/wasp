<?php namespace App\Models\Users;

class User extends \Wasp\Model
{
    protected static $buffer;
    protected $table = 'users';

    public static function getById( $id )
    {
        if( empty(self::$buffer) && !is_array(self::$buffer) ) {
            self::$buffer = [];
        }
        
        if( !empty(self::$buffer[$id]) ) {
            return self::$buffer[ $id ];
        }
        
        return self::$buffer[ $id ] = self::where('id', '=', (int)$id)
                                          ->with('profile', 'session', 'group', 'contacts')
                                          ->first();
    }
    
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
                    ->where('type', '=', Contact::TYPE_EMAIL);
    }
    
    public function getEmailAttribute()
    {
        $_ = $this->emails()
                  ->where('default', '=', 1)
                  ->first();
        
        if( !empty($_) ) {
            return $_->value;
        }
        
        return false;
    }
}