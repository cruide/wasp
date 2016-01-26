<?php namespace App\Services;

class Auth extends \Wasp\Service
{
    public function execute()
    {
        $ctrl = $this->router->getControllerName();

        if( strtolower($ctrl) == 'auth' ) {
            \Wasp\Theme::mySelf()->setLayout('layout.auth');
        }
    }
}
