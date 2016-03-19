<div class="page-header">
  <h3><span class="glyphicon glyphicon-user"></span> {'user_profile'|l}</h3>
</div>

{if not empty($usersave)}
<div class="alert alert-success alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>Отлично!</strong> Данные пользователя успешно изменены.
</div>
{/if}

{if not empty($errors)}
<div class="alert alert-warning alert-dismissable">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>Внимание!</strong> При заполнении формы были допущены ошибки.<br />
    <ul>
    {foreach $errors as $key=>$val}
        {if array_count($val) > 0}
        <li>{$key}:
            <ul>
            {foreach $val as $error}
                <li>{$error}</li>
            {/foreach}
            </ul>
        </li>
        {/if}
    {/foreach}
    </ul>
</div>
{/if}                        

<div class="col-xs-10">
    <form role="form" class="form-horizontal col-xs-10" action="{$base_url}/profile" method="post">
        <input type="hidden" name="confirm" value="ok" />

        <div class="form-group{if not empty($errors.nicname)} has-error{/if}">
            <label for="user-nicname" class="control-label col-xs-4">{'nickname'|ufl}:</label>

            <div class="col-xs-8">
                <input type="text" name="form[nicname]" class="form-control" id="user-nicname" value="{if !empty($errors.nicname)}{$errors.nicname|escape}{else}{$user->profile->nicname}{/if}" maxlength="32" />
            </div>
        </div>

        <div class="form-group{if not empty($errors.first_name)} has-error{/if}">
            <label for="user-first-name" class="control-label col-xs-4">
                {'first_name'|ufl}:
            </label>

            <div class="col-xs-8">
                <input type="text" name="form[first_name]" class="form-control" id="user-first-name" value="{if !empty($errors.first_name)}{$errors.first_name|escape}{else}{$user->profile->first_name}{/if}" maxlength="32" />
            </div>
        </div>

        <div class="form-group{if !empty($errors.middle_name)} has-error{/if}">
            <label for="user-middle-name" class="control-label col-xs-4">{'middle_name'|ufl}:</label>

            <div class="col-xs-8">
                <input type="text" name="form[middle_name]" class="form-control" id="user-middle-name" value="{if !empty($errors.middle_name)}{$errors.middle_name|escape}{$user->profile->middle_name}{/if}" maxlength="32" />
            </div>
        </div>

        <div class="form-group{if !empty($errors.last_name)} has-error{/if}">
            <label for="user-last-name" class="control-label col-xs-4">{'last_name'|ufl}:</label>

            <div class="col-xs-8">
                <input type="text" name="form[last_name]" class="form-control" id="user-last-name" value="{if !empty($errors.last_name)}{$errors.last_name|escape}{$user->profile->last_name}{/if}" maxlength="32" />
            </div>
        </div>

        <div class="form-group{if not empty($errors.birthday)} has-error{/if}">
            <label for="user-birthday" class="control-label col-xs-4">{'birthday'|ufl}</label>

            <div class="col-xs-8">
                <input type="text" 
                       name="form[birthday]" 
                       id="user-birthday" 
                       class="form-control"
                       data-date="{$user->profile->birthday|date_format:'d.m.Y'}" 
                       data-date-format="dd.mm.yyyy"
                       value="{$user->profile->birthday|date_format:'d.m.Y'}" 
                       maxlength="32"
                       readonly="readonly" />
            </div>
        </div>
        
        <div class="form-group{if !empty($errors.gender)} has-error{/if}">
            <label for="user-gender" class="control-label col-xs-4">{'gender'|ufl}:</label>

            <div class="col-xs-8">
                <select name="form[gender]" class="form-control" id="user-gender">
                    <option value="MALE"{if $user->profile->gender eq 'MALE'} selected="selected"{/if}>{'male'|ufl}</option>
                    <option value="FEMALE"{if $user->profile->gender eq 'FEMALE'} selected="selected"{/if}>{'female'|ufl}</option>
                    <option value="OTHER"{if $user->profile->gender eq 'OTHER'} selected="selected"{/if}>{'other'|ufl}</option>
                </select>
            </div>
        </div>

        <div class="form-group{if !empty($errors.password)} has-error{/if}">
            <label for="user-passwd1" class="control-label col-xs-4">{'password'|ufl}:</label>

            <div class="col-xs-8">
                <input type="password" name="form[passwd1]" class="form-control" id="user-passwd1" maxlength="16" />
            </div>
        </div>

        <div class="form-group{if !empty($errors.password)} has-error{/if}">
            <label for="user-passwd2" class="control-label col-xs-4">Повторить пароль:</label>

            <div class="col-xs-8">
                <input type="password" name="form[passwd2]" class="form-control" id="user-passwd2" maxlength="16" />
            </div>
        </div>

        <div class="col-xs-10"></div>
        <div class="col-xs-2">
            <button type="submit" class="btn btn-primary">
                <span class="glyphicon glyphicon-floppy-save"></span>&nbsp;
                {'save'|ufl}
            </button>
        </div>
        
    </form>
</div>
<script type="text/javascript">
$(function(){
    $('#user-birthday').datepicker();
});
</script>