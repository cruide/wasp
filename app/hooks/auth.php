<?php namespace App\Hooks;

class Auth extends \Wasp\Hook
{
    public function execute()
    {
        $ctrl = $this->router->getControllerName();

        if( strtolower($ctrl) == 'auth' ) {
            \Wasp\Theme::mySelf()->setLayout('layout.auth');
        }
    }
}
