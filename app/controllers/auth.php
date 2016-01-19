<?php namespace App\Controllers;

use Wasp\Validator;
use Wasp\Controller;
use App\Library\Auth as AuthLibrary;

class Auth extends Controller
{
    private $auth;
    
    public function _before()
    {
        $this->auth = AuthLibrary::mySelf();

        if( $this->auth->isAuth() && $this->router->getMethodName() != 'Signout' ) {
            redirect();
        }
    }
    
    public function actionDefault()
    {
        return $this->ui->fetch('auth/default');
    }
    
    public function postSignin()
    {
        $confirm = $this->input->post('confirm');
        $form    = $this->input->post('form');

        if( $confirm != 'ok' ) {
            redirect(['controller' => 'auth']);
        }

        $validator = new Validator([
            'email'    => $form['email'],
            'password' => $form['password'],
        ], [
            'email'    => 'required|email',
            'password' => 'required|alphanum|minlen:4|maxlen:16',
        ]);
        
        if( $validator->checkAll() && $this->auth->signin($form['email'], $form['password']) ) {
            redirect();
        }
        
        $this->ui->assign('error', l('authError') );

        return $this->ui->fetch('auth/default');
    }
    
    public function actionSignout()
    {
        $this->auth->signout();
        redirect(['controller' => 'auth']);
    }
}