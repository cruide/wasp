<div class="page-header">
  <h3><span class="glyphicon glyphicon-list-alt"></span> Список пользователей</h3>
</div>

<div class="col-xs-12">
    <table class="table table-striped" id="users-table">
        <thead>
            <tr>
                <th style="width: 5%;"> # </th>        
                <th style="width: 45%;"> Email </th>        
                <th style="width: 15%;"> {'group'|ufl} </th>        
                <th style="width: 25%;"> Last time </th>        
                <th style="width: 10%;"> --- </th>        
            </tr>
        </thead>
        
        <tbody>
        {foreach from=$users key=key item=val}
            <tr>
                <td style="width: 5%;">{$val->id}</td>
                <td style="width: 45%;">{$val->email}</td>
                <td style="width: 15%;">{$val->group->name}</td>
                <td style="width: 25%;">{if !empty($val->session->stamp)}{$val->session->stamp|date_format:'d.m.Y H:i:s'}{/if}</td>
                <td style="width: 10%;">
                    {if $val->group->level < $auth_user->group->level or $auth->isAdmin() or $auth_user->id == $val->id}
                    <span class="glyphicon glyphicon-pencil" data-toggle="tooltip" data-placement="top" title="{'edit'|ufl}" style="cursor: pointer;" onclick="wasp.redirect( wasp.url + '/users/edit/id/{$val->id}' )"></span>
                    &nbsp;
                        {if $auth_user->id != $val->id }
                        <a data-toggle="tooltip" data-placement="top" title="{'delete'|ufl}" style="cursor: pointer;" onclick="wasp.fn.user_delete({$key}, '{$val->email}')">
                            <span class="glyphicon glyphicon-remove"></span>
                        </a>
                        {/if}
                    {/if}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>    

<script type="text/javascript">
wasp.fn.user_delete = function(id, email) {
    if( typeof(id) == 'undefined' || typeof(id) == 'email' || id == '' || email == '' ) {
        return false;
    }
    
    wasp.confirm('Вы действительно желаете удалить пользователя ' + email + '?', 'Удалить пользователя?', function(){
        wasp.ajax('{$base_url}/?do=users-delete', function(r){
            if( r == 'success' ) {
                wasp.redirect('{$base_url}/?do=users');
            } else {
                wasp.message('Удаление не удалось. Что-то пошло не так.');
            }
        }, {
            'id': id
        });
    });
}

$(function(){ $('#users-table span').tooltip() });
</script>    
