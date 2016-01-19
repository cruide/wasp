<?php

\Wasp\Input::on('id', 'get', function($value) {
    if( !is_numeric($value) ) {
        return null;
    }
    
    return $value;
});

if( \Wasp\Router::mySelf()->getControllerName() != 'Install' ) {
    \App\Library\Auth::mySelf();
}
