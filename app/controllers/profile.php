<?php namespace App\Controllers;

use Wasp\Controller;
use App\Models\UsersGroup;

class Profile extends Controller
{
    private $onpage = 25;
    private $users;
    
    private $validation = [
        'gender'      => 'required|alpha|minlen:4|maxlen:6',
        'nicname'     => 'alphanum|minlen:4|maxlen:32',
        'first_name'  => 'name|minlen:2|maxlen:255',
        'middle_name' => 'name|minlen:2|maxlen:255',
        'last_name'   => 'name|minlen:2|maxlen:255',
        'birthday'    => 'date',
        'passwd1'     => 'alphanum|minlen:6|maxlen:16',
        'passwd2'     => 'alphanum|minlen:6|maxlen:16',
    ];
    
// ------------------------------------------------------------------------------
    /**
    * Этот метод будет запущен перед выполнением
    * любого экшена
    */
    public function _before()
    {
        if( !\App\Library\Auth::mySelf()->isAdmin() ) {
            redirect(['controller' => 'index']);
        }
    }
// ------------------------------------------------------------------------------
    /**
    * Метод-экшен по умолчанию
    * 
    */
    public function actionDefault()
    {
        $confirm = $this->input->post('confirm');
        $form    = $this->input->post('form');
        $errors  = [];
        $user    = \App\Library\Auth::mySelf()->getAuthUser();
        
        if( !empty($confirm) && $confirm == 'ok' ) {
            $validator = new \Wasp\Validator($form, $this->validation);
            
            if( !$validator->checkAll() ) {
                $errors = $validator->getMessages();
                
            } else {
                $profile = $user->profile;
                
                foreach($profile->toArray() as $key=>$val) {
                    if( array_key_isset($key, $form) ) {
                        $profile->$key = $form[ $key ];
                    }
                }
                
                $profile->save();
                                
                redirect([
                    'controller' => 'profile',
                    'method'     => 'default',
                    'id'         => $id,
                ]);
            }
        }
        
        $this->layout
             ->useThemeCss('datepicker.css')
             ->useThemeJs('bootstrap-datepicker.js', false);
        
        return $this->ui
                    ->assign('errors', $errors)
                    ->assign('user'  , $user)
                    ->assign('groups', UsersGroup::get())
                    ->fetch('profile/index');
    }

}