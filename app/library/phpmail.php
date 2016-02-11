<?php namespace App\Library;
/**
* @author     Tishchenko Alexander (info@alex-tisch.ru)
* @copyright  Copyright (c) 2015 All rights to Tishchenko A.
* @package    WASP - MVC micro-framework for PHP application
*/

class Phpmail extends \Wasp\Library
{
    protected $phpmail, $settings, $ui;
// -------------------------------------------------------------------------------------
    protected function _prepare()
    {
        $this->settings = cfg('phpmailer');
        
        if( !class_exists('\\PHPMailer') ) {
            require('phpmailer' . DIR_SEP . 'class.phpmailer.php');
        }
        
        $mails_path    = APP_DIR . DIR_SEP . 'mails';
        $this->phpmail = new \PHPMailer($exceptions);
        $this->ui      = new \Wasp\Native( $mails_path );

        if( !empty($this->settings->email_from) ) {
            $this->phpmail->SetFrom(
                $this->settings->email_from,
                (isset($this->settings->from_name)) ?
                    $this->settings->from_name : ''
            );
        }

        if( !empty($this->settings->replyto) ) {
            $this->phpmail->AddReplyTo( $this->settings->replyto );
        }

        if( !empty($this->settings->host) ) {
            $this->phpmail->Host = $this->settings->host;
        }
        
        if( !empty($this->settings->port) ) {
            $this->phpmail->Port = $this->settings->port;
        }
        
        if( !empty($this->settings->username) ) {
            $this->phpmail->Username = $this->settings->username;

            if( !empty($this->settings->password) ) {
                $this->phpmail->Password = $this->settings->password;
                $this->phpmail->SMTPAuth = true;
            }
        }
        
        if( !empty($this->settings->authtype) ) {
            $this->phpmail->AuthType = $this->settings->authtype;
        }
        
        if( !empty($this->settings->type) ) {
            switch($this->settings->type) {
                case 'sendmail': 
                   if( isset($this->settings->sendmail) ) { 
                       $this->phpmail->Sendmail = (string)$this->settings->sendmail; 
                   }
                   
                   $this->phpmail->IsSendmail(); 
                   break;
                   
                case 'smtp':     $this->phpmail->IsSMTP(); break;
                case 'qmail':    $this->phpmail->IsQmail(); break;
                default:         $this->phpmail->IsMail();
            }
        } else {
            $this->phpmail->IsMail();
        }

        $this->phpmail->XMailer = FRAMEWORK;
        $this->phpmail->CharSet = 'utf-8';
    }
// -------------------------------------------------------------------------------------
    public function subject($subject)
    {
        $this->phpmail->Subject = htmlspecialchars( $subject );
        return $this;
    }
// -------------------------------------------------------------------------------------
    public function assign($tpl_var, $value = null, $nocache = false)
    {
        $this->ui->assign($tpl_var, $value, $nocache);
        return $this;
    }
// -------------------------------------------------------------------------------------
    public function send($template)
    {
        $this->phpmail->MsgHTML(
            $this->ui->fetch($template)
        );

        try {
            $_ = $this->phpmail->send();
        } catch( \Wasp\Exception $e ) {
            wasp_error( $e->getMessage() );
        }
        
        return $_;
    }
// -------------------------------------------------------------------------------------
    public function __call($name, $args)
    {
        if( method_exists($this->phpmail, $name) ) {
            return call_user_func_array(
                array($this->phpmail, $name),
                $args
            );
        } else {
            throw new \SB\Exception\Phpmail(
                "Call to undefined method {$name}"
            );
        }
    }
}
