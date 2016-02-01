<?php namespace App\Controllers;

use App\Models\User;
use App\Models\UsersGroup;
use App\Library\Auth;

class Users extends \Wasp\Controller
{
    private $onpage = 25;
    private $users;
    
    private $validation = [
        'email'       => 'required|email|minlen:4|maxlen:255',
        'blocked'     => 'required|numeric|max:2',
        'gender'      => 'required|alpha|minlen:4|maxlen:6',
        'nicname'     => 'alphanum|minlen:4|maxlen:32',
        'first_name'  => 'name|minlen:2|maxlen:255',
        'middle_name' => 'name|minlen:2|maxlen:255',
        'last_name'   => 'name|minlen:2|maxlen:255',
        'birthday'    => 'date',
        'group_id'    => 'numeric|minlen:1',
    ];
    
// ------------------------------------------------------------------------------
    /**
    * Этот метод будет запущен перед выполнением
    * любого экшена
    */
    public function _before()
    {
        if( !Auth::mySelf()->isAdmin() ) {
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
        $page = $this->input->get('p');
        if( empty($page) || !is_numeric($page) ) {
            $page = 1;
        }

        $from  = ceil( ($page - 1) * $this->onpage );
        $users = User::skip($from)->take( $this->onpage )->get();
        
        return $this->ui
                    ->assign('users', $users)
                    ->fetch('users/index');
    }
// ------------------------------------------------------------------------------
    public function actionEdit( $id = null )
    {
        if( empty($id) || !is_numeric($id) ) {
            redirect();
        }

        $user = User::find( $id );
        
        if( empty($user->id) ) {
            redirect();
        }

        $confirm = $this->input->post('confirm');
        $form    = $this->input->post('form');
        $errors  = [];

        
        if( !empty($confirm) && $confirm == 'ok' ) {
            $validator = new \Wasp\Validator($form, $this->validation);

            if( !$validator->checkAll() ) {
                $errors = $validator->getMessages();
            }
            
            if( array_count($errors) == 0 ) {
                
                $current_user = $this->auth->getAuthUser();
                $group_level  = $this->users->getGroupLevel( $form['group_id'] );
                $user_data    = [
                    'birthday' => wasp_date_format($form['birthday'], 'Y-m-d'),
                ];
                
                if( $this->users->groupIdExists($form['group_id']) && ($group_level < $current_user->group->level || $this->auth->is_root()) ) {
                    if( $user->id != $this->auth->getAuthUserId() ) {
                        $user_data['group_id'] = intval($form['group_id']);
                        $user_data['blocked']  = intval($form['blocked']);
                    }
                }
                
                if( $user->id == $current_user->id || $this->auth->isAdmin() ) {
                    foreach($form as $key=>$val) {
                        if( !array_key_isset($key, $user_data) ) {
                            $user_data[ $key ] = $val;
                        }
                    }
                }
                
                $this->users->update($user_data);
                
                redirect([
                    'controller' => 'users',
                    'method'     => 'edit',
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
                    ->fetch('users/edit');
    }

// ------------------------------------------------------------------------------
    public function actionAdd()
    {
        $confirm = $this->input->post('confirm');
        $form    = $this->input->post('form');
        $errors  = [];

        if( !empty($confirm) && $confirm == 'ok' ) {
            $validator = new \Wasp\Validator($form, $this->validation);
            
            if( !$validator->checkAll() ) {
                $errors = $validator->getMessages();
            }
            
            $check_user = $this->users->getByEmail( $form['email'] );
            $user_data  = [];
            
            if( !empty($check_user->id) ) {
                if( !isset($errors['email']) || !is_array($errors['email']) ) {
                    $errors['email'] = [];
                }
                
                $errors['email'][] = 'Такой пользователь уже есть';
            }
            
            if( !is_alphanum($form['passwd1']) || !is_alphanum($form['passwd2'])
             || wasp_strlen($form['passwd1']) > 16 || wasp_strlen($form['passwd1']) < 6
             || wasp_strlen($form['passwd2']) > 16 || wasp_strlen($form['passwd2']) < 6
             || $form['passwd1'] != $form['passwd2'] )
            {
                if( !isset($errors['passwd1']) || !is_array($errors['passwd1']) ) {
                    $errors['passwd1'] = [];
                }
                
                $errors['passwd1'][] = 'Неверное указан пароль';
            }
            
            if( array_count($errors) == 0 ) {

                $current_user = $this->auth->getAuthUser();
                $group_level  = $this->users->getGroupLevel( $form['group_id'] );
                $passwd       = password_crypt($form['passwd1']);

                $user_data['email']    = $form['email'];
                $user_data['password'] = $passwd;
                
                if( $this->users->groupIdExists($form['group_id']) && ($group_level < $current_user->group->level || $this->auth->isRoot()) ) {
                    $user_data['group_id'] = intval($form['group_id']);
                    $user_data['blocked']  = intval($form['blocked']);
                }
                
                if( $this->auth->isAdmin() ) {
                    foreach($form as $key=>$val) {
                        if( !array_key_isset($key, $user_data) ) {
                            $user_data[ $key ] = $val;
                        }
                    }
                }

                $id = $this->users->create($user_data);
                    
                redirect([
                    'controller' => 'users',
                    'method'     => 'edit',
                    'id'         => $id,
                    'message'    => 'Пользователь успешно добавлен в систему.',
                ]);
            }
        }
        
        $this->layout
             ->useThemeCss('datepicker.css')
             ->useThemeJs('bootstrap-datepicker.js', false);

        return $this->ui
                    ->assign('errors', $errors)
                    ->assign('form'  , $form)
                    ->assign('groups', $this->users->getGroups())
                    ->fetch('users/add');
    }
// ------------------------------------------------------------------------------
    public function actionDelete()
    {
        $id      = $this->input->post('id');
        $auth    = $this->auth;
        $errors  = [];

        if( empty($id) || !is_numeric($id) || !$auth->isAdmin() ) {
            return 'fail';
        }

        $user = $this->users->getById($user);
        
        if( empty($user->email) ) {
            return 'fail';
        }
        
        if( $user->type == 'ADMIN' || $user->type == 'ROOT' ) {
            if( $user->type == 'ADMIN' && $auth->isRoot() ) {
                if( $this->models->users->deleteById($id) ) {
                    return 'success';
                } else {
                    return 'fail';
                }
            }

            if( $user->type == 'ROOT' && $auth->is_root() && $auth->get_auth_user_id() != $user->id ) {
                if( $this->models->users->delete_by_id($id) ) {
                    return 'success';
                } else {
                    return 'fail';
                }
            }
        }
        
        if( $user->type != 'ADMIN' && $user->type != 'ROOT' && $this->models->users->delete_by_id($id) ) {
            return 'success';
        }
        
        return 'fail';
    }

}