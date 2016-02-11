<?php namespace App\Services;

class AuthLayout extends \Wasp\Service
{
    public function execute()
    {
        $ctrl = $this->router->getControllerName();

        if( strtolower($ctrl) == 'auth' ) {
            theme()->setLayout('layout.auth');
        }
    }
}
