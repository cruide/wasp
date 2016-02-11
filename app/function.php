<?php

function auth()
{
    return \App\Library\Auth::mySelf();
}

function is_auth()
{
    return auth()->isAuth();
}