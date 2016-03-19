<form class="form-signin" role="form" action="{$base_url}/auth/signin" method="post">
    <input type="hidden" name="confirm" value="ok" />

    <h2 class="form-signin-heading">{'authorization'|l}</h2>

    {if not empty($errors)}
        {foreach $errors as $key=>$error}
    <div class="alert alert-warning alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>{$key}:</strong> {$error}
    </div>
        {/foreach}
    {/if}
    
    <input type="email" name="form[email]" class="form-control" placeholder="{'user'|ufl}" required autofocus />
    <input type="password" name="form[password]" class="form-control" placeholder="{'password'|ufl}" required />

    <br />
    
    <button class="btn btn-lg btn-primary btn-block" type="submit">
        <span class="glyphicon glyphicon-log-in"></span> 
        {'enter'|ufl}
    </button>
</form>
