<?php namespace App\Library;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

use App\Models\Users\User;
use App\Models\Users\Session as UserSession;
use App\Models\Users\Contact as UserContact;

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
        $smarty             = new \Smarty();
        
        $obj = UserSession::where('session_id', '=', $session_id)
                          ->where('ip_address', '=', get_ip_address())
                          ->first();

        if( !empty($obj->id) ) {
            $obj->stamp = time();
            $obj->save();
            
            $this->autorization = true;
            $this->user         = $obj->user;

            $smarty->assignGlobal('auth_user', $this->user);
            
            unset($obj);
        }

        $smarty->assignGlobal('auth', $this);
    }
// -----------------------------------------------------------------------------
    public function signin($id, $password, $type = UserContact::TYPE_EMAIL)
    {
        if( !is_scalar($id) ) {
            return false;
        }

        $auth = UserContact::where('type' , '=', $type)
                           ->where('value', '=', $id)
                           ->first();
                         
        if( empty($auth) ) {
            return false;
        }
        
        $obj = User::where('id', '=', $auth->user_id)
                   ->where('password', '=', password_crypt($password))
                   ->with('session')
                   ->first();
        
        if( !empty($obj->id) ) {
            $session = $obj->session;

            if( empty($session->id) ) {
                $session = new UserSession();
            }
            
            $session->session_id = $this->session->id(true);
            $session->user_id    = $obj->id;
            $session->ip_address = get_ip_address();
            $session->stamp      = time();
            $session->save();
            
            $this->autorization = true;
            $this->user         = $session->user;
            
            $smarty = new \Smarty();
            $smarty->assignGlobal('auth_user', $this->user);
            $smarty->assignGlobal('auth'     , $this);
            
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
                
                $smarty = new \Smarty();
                $smarty->assignGlobal('auth_user', null);
                $smarty->assignGlobal('auth'     , null);
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
