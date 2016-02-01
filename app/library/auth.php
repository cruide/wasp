<?php namespace App\Library;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

use Wasp\Native;
use App\Models\User;
use App\Models\UsersSession;
use App\Models\UsersContact;

class Auth extends \Wasp\Library
{
    private $autorization, $admin, $root, $user;
    private static $_instance;
// -----------------------------------------------------------------------------
    public function _prepare()
    {
        $this->autorization = false;
        $this->admin        = false;
        $this->root         = false;
        $session_id         = $this->session->id(true);

        $obj = UsersSession::where('session_id', '=', $session_id)
                           ->where('ip_address', '=', get_ip_address())
                           ->first();

        if( !empty($obj->id) ) {
            $obj->stamp = time();
            $obj->save();
            
            $this->autorization = true;
            $this->user         = $obj->user;

            Native::assignGlobal('authUser', $this->user);
            
            unset($obj);
        }
        
        Native::assignGlobal('authLib', $this);
        class_alias('\\App\\Library\\Auth', '\\Auth');
    }
// -----------------------------------------------------------------------------
    public function signin($id, $password, $type = UsersContact::TYPE_EMAIL)
    {
        if( !is_scalar($id) ) {
            return false;
        }

        $auth = UsersContact::where('type' , '=', $type)
                            ->where('value', '=', $id)
                            ->first();
                         
        if( empty($auth) ) {
            return false;
        }
        
        $obj = User::where('id', '=', $auth->user_id)
                   ->where('password', '=', password_crypt($password))
                   ->first();
        
        if( !empty($obj->id) ) {
            $session = $obj->session;

            if( empty($session->id) ) {
                $session = new UsersSession();
            }
            
            $session->session_id = $this->session->id(true);
            $session->user_id    = $obj->id;
            $session->ip_address = get_ip_address();
            $session->stamp      = time();
            $session->save();
            
            $this->autorization = true;
            $this->user         = $session->user;

            Native::assignGlobal('authUser', $this->user);
            Native::assignGlobal('authLib' , $this);
            
            return true;
        }
        
        
        return false;
    }
// -----------------------------------------------------------------------------
    public function signout()
    {
        if( $this->autorization ) {
            $obj = $this->user->session;
            
            if( !empty($obj->session_id) && !empty($obj->ip_address) ) {
                $obj->session_id = '';
                $obj->save();

                $this->user         = null;
                $this->autorization = false;
                
                $this->session->destroy();
            }
        }
    }
// -----------------------------------------------------------------------------
    public function getAuthUser()
    {
        if( $this->autorization ) {
            return $this->user;
        }
        
        return false;
    }
// -----------------------------------------------------------------------------
    public function getAuthUserId()
    {
        if( $this->autorization ) {
            return $this->user->id;
        }
        
        return false;
    }
// -----------------------------------------------------------------------------
    public function isAuth()
    {
        return $this->autorization;
    }
// -----------------------------------------------------------------------------
    public function isBlocked()
    {
        if( $this->autorization && $this->user->blocked == 1 ) {
            return true;
        }
        
        return false;
    }
// -----------------------------------------------------------------------------
    public function isAdmin()
    {
        if( $this->isAuth() && $this->user->group->level >= 70 ) {
            return true;
        }
        
        return false;
    }
// -----------------------------------------------------------------------------
    public function isSU()
    {
        if( $this->isAuth() && $this->user->group->level >= 99 ) {
            return true;
        }
        
        return false;
    }
// -----------------------------------------------------------------------------
    public static function mySelf()
    {
        if( null === self::$_instance ) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
