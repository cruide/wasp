<?php namespace App\Controllers;

use Wasp\Controller;

use App\Models\User;
use App\Models\UsersProfile;
use App\Models\UsersContact;
use App\Models\UsersGroup;

class Install extends Controller
{
    protected $groups = [
        99 => 'Superusers',
        80 => 'Administrators',
        60 => 'Moderators',
        40 => 'Advanced users',
        20 => 'Users',
        10 => 'Noobs',
    ];
    
    public function actionDefault()
    {
        $schema = \Wasp\DB::schema();
        
        if( !$schema->hasTable('users') ) {
            $schema->create('users', function( $table ) {
                $table->engine = 'InnoDB';
                
                $table->bigIncrements('id');
                $table->integer('group_id')->default(0)->index();    
                $table->string('password', 48)->default('')->index();
                $table->tinyInteger('blocked')->default(0)->index();    
                $table->bigInteger('blocked_to')->default(0)->index();    
                $table->timestamps(); 
                $table->softDeletes();
            });
        }
        
        if( !$schema->hasTable('users_contacts') ) {
            $schema->create('users_contacts', function( $table ) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->index()->default(0);
                $table->tinyInteger('type')->index()->default( UsersContact::TYPE_EMAIL );    
                $table->string('value')->index()->default('');    
                $table->timestamps(); 
                $table->softDeletes();
            });
        }
        
        if( !$schema->hasTable('users_sessions') ) {
            $schema->create('users_sessions', function( $table ) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->index()->default(0);
                $table->string('session_id', 48)->index()->default('');    
                $table->string('ip_address', 12)->index()->default('');
                $table->bigInteger('stamp')->index()->default(0);
                $table->timestamps(); 
                $table->softDeletes();
            });
        }
        
        if( !$schema->hasTable('users_profiles') ) {
            $schema->create('users_profiles', function( $table ) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->index()->default(0);
                $table->string('nicname', 32)->index()->default('');    
                $table->string('first_name', 150)->index()->default('');
                $table->string('middle_name', 150)->index()->default('');
                $table->string('last_name', 150)->index()->default('');
                $table->enum('gender', ['MALE','FEMALE','OTHER'])->index()->default('MALE');
                $table->date('birthday')->index();
                $table->bigInteger('stamp')->index()->default(0);
                $table->timestamps(); 
                $table->softDeletes();
            });
        }
        
        if( !$schema->hasTable('users_groups') ) {
            $schema->create('users_groups', function( $table ) {
                $table->increments('id');
                $table->integer('parent_id')->index()->default(0);
                $table->string('name', 50)->index()->default('');    
                $table->string('desctiption')->index()->default('');
                $table->smallInteger('level')->index()->default(0);
                $table->string('lng')->default('');
                $table->timestamps(); 
                $table->softDeletes();
            });
        }
        
        /**
        * Install groups
        */
        foreach($this->groups as $level=>$name) {
            $group        = new UsersGroup();
            $group->name  = $name;
            $group->level = $level;
            $group->lng   = snake_case($name);
            $group->save();
        }
        
        unset($level, $name, $group);
        
        /**
        * Create SuperUser
        */
        $user = new User();
        $user->group_id = 1;
        $user->password = password_crypt('toor');
        $user->save();
        
        $userAuth = new UsersContact();
        $userAuth->user_id = $user->id;
        $userAuth->type    = UsersContact::TYPE_EMAIL;
        $userAuth->value   = 'root@wasp.cms';
        $userAuth->save();
        
        $userProfile = new UsersProfile();
        $userProfile->user_id = $user->id;
        $userProfile->nicname = 'SuperUser';
        $userProfile->save();
        
        return 'Installation success!';
    }
    
    public function actionDeinstall()
    {
        $schema = \Wasp\DB::schema();
        
        $schema->dropIfExists('users');
        $schema->dropIfExists('users_auths');
        $schema->dropIfExists('users_sessions');
        $schema->dropIfExists('users_profiles');
        $schema->dropIfExists('users_groups');

        return 'Deinstallation success!';
    }
}
  

