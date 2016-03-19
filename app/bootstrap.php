<?php

if( router()->getControllerName() != 'Install' ) {
    auth();
}
