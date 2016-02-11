<?php

\Wasp\Input::on('id', 'get', function($value) {
    if( !is_numeric($value) ) {
        return null;
    }
    
    return $value;
});

if( router()->getControllerName() != 'Install' ) {
    auth();
}
