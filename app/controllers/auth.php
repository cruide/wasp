<?php namespace App\Controllers;

use Wasp\Validator;
use Wasp\Controller;

class Auth extends Controller
{
    public function _before()
    {
        if( is_auth() && $this->router->getMethodName() != 'Signout' ) {
            redirect();
        }
    }
    
    public function anyDefault()
    {
        return $this->ui->fetch('auth/default.tpl');
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
            'password' => 'required|alphanum|minlen:4|maxlen:16|message:Только цифры и латинские буквы',
        ]);
        
        if( $validator->checkAll() && auth()->signin($form['email'], $form['password']) ) {
            redirect();
        }
        
        $this->ui->assign('error', l('authError') );
        $this->ui->assign('errors', $validator->getMessages() );

        return $this->ui->fetch('auth/default.tpl');
    }
    
    public function anySignout()
    {
        auth()->signout();
        redirect(['controller' => 'auth']);
    }
}