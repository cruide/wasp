<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="generator" content="{$framework}">
        <meta name="author" content="Tishchenko Alexander">
        <!--<link rel="shortcut icon" href="{$theme_url}/favicon.ico">-->

        <title>Wasp php mini-framework</title>

        <script type="text/javascript" src="{$js_url}/jquery-2.2.2.min.js"></script>
        <script type="text/javascript" src="{$base_url}/js"></script>

        <!-- Bootstrap core CSS -->
        <!--<link href="{$css_url}/bootstrap.min.css" rel="stylesheet" />-->
        <link href="{$css_url}/bootstrap.spacelab.min.css" rel="stylesheet" />
        <link href="{$css_url}/bootstrap-theme.min.css" rel="stylesheet" />
        <link href="{$css_url}/font-awesome.min.css" rel="stylesheet" />

        <!-- Custom styles for this template -->
        <link href="{$css_url}/style.css" rel="stylesheet">

        <!-- Just for debugging purposes. Don't actually copy this line! -->
        <!--[if lt IE 9]>
        <script src="{$js_url}/ie8-responsive-file-warning.js"></script>
        <![endif]-->

        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="{$js_url}/html5shiv.js"></script>
        <script src="{$js_url}/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <div class="container">
            <div class="header" style="margin-bottom: 0;">
                
                <ul class="nav nav-pills pull-right">
                    <li{if $action eq 'index/default'} class="active"{/if}>
                        <a href="{$base_url}">
                            <span class="glyphicon glyphicon-home"></span>&nbsp;
                            {'home'|ufl}
                        </a>
                    </li>

                    {if not empty($auth) and $auth->isAuth()}
                        {if $auth->isAdmin() }
                    <li class="dropdown{if $controller_name eq 'users'} active{/if}">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="{'/users'|make_url}">
                            <span class="fa fa-users"></span>&nbsp; 
                            {'users'|ufl}&nbsp; 
                            <span class="caret"></span>
                        </a>
                        
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{'/users'|make_url}">
                                    <span class="fa fa-list"></span>&nbsp;
                                    {'list'|ufl}
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="{'/users/add'|make_url}">
                                    <span class="fa fa-user-plus"></span>&nbsp;
                                    {'add'|ufl}
                                </a>
                            </li>
                        </ul>
                    </li>
                        {/if}
                    
                    <li{if $action eq 'profile/default'} class="active"{/if}>
                        <a href="{'/profile'|make_url}">
                            <i class="fa fa-user"></i>&nbsp;
                            {'profile'|ufl}
                        </a>
                    </li>

                    <li>
                        <a href="{'/auth/signout'|make_url}">
                            <span class="glyphicon glyphicon-log-out"></span>&nbsp;
                            {'exit'|ufl}
                        </a>
                    </li>
                    {else}
                    <li>
                        <a href="{'/auth'|make_url}">
                            <span class="glyphicon glyphicon-log-in"></span>&nbsp;
                            {'enter'|ufl}
                        </a>
                    </li>
                    {/if}
                </ul>
                
                <h1 class="text-muted">
                    <img src="{$images_url}/wasp.png" alt="wasp" style="width: 38px;" />
                    WASP
                </h1>
            </div>

            <div class="row marketing" id="main-content" style="margin-top: 0;">
                {if not empty($redirect_message) }
                <div class="alert alert-info">
                    {$redirect_message}
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
                {/if}

                {if not empty($redirect_error) }
                <div class="alert alert-danger">
                    {$redirect_error}
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                </div>
                {/if}

                {$content}
            </div>

            <div class="footer">
                <p>Wasp PHP micro-framework by Alex Tisch &copy; 2015, <!-- DEBUG-INFO --></p>
            </div>
        </div> <!-- /container -->
        <div id="body-script-buffer"></div>

        <script type="text/javascript" src="{$js_url}/bootstrap.min.js"></script>
        <script type="text/javascript">$(function(){ $('.alert').alert(); });</script>
    </body>
</html>