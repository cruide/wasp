<?php

function auth()
{
    return \App\Library\Auth::mySelf();
}
// --------------------------------------------------------------------------------
function is_auth()
{
    return auth()->isAuth();
}
// --------------------------------------------------------------------------------
function is_wasp_ajax()
{
    global $_SERVER;

    if( is_ajax() ) {
        if( isset($_SERVER['HTTP_X_REQUEST_TYPE']) && $_SERVER['HTTP_X_REQUEST_TYPE'] == 'Expedited' ) {
            return true;
        }
    }
  
    return false;
}                
