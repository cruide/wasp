<?php namespace App\Controllers;

use Wasp\Controller;

use App\Models\Users\User;
use App\Models\Users\Profile;
use App\Models\Users\Contact;
use App\Models\Users\Group;

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
    
    public function anyDefault()
    {
        global $database;
        
        $schema = $database->connection()->getSchemaBuilder();
        
        if( !$schema->hasTable('user_groups') ) {
            $schema->create('user_groups', function( $table ) {
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
        
        if( !$schema->hasTable('users') ) {
            $schema->create('users', function( $table ) {
                $table->bigIncrements('id');
                $table->integer('group_id')->index()->default(0);    
                $table->string('password', 48)->default('')->index();
                $table->tinyInteger('blocked')->default(0)->index();    
                $table->bigInteger('blocked_to')->default(0)->index();    
                $table->timestamps(); 
                $table->softDeletes();
            });
        }
        
        if( !$schema->hasTable('user_contacts') ) {
            $schema->create('user_contacts', function( $table ) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->index()->default(0);
                $table->tinyInteger('type')->index()->default( Contact::TYPE_EMAIL );    
                $table->string('value')->index()->default('');    
                $table->tinyInteger('default')->index()->default(0);    
                $table->timestamps(); 
                $table->softDeletes();
            });
        }
        
        if( !$schema->hasTable('user_sessions') ) {
            $schema->create('user_sessions', function( $table ) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->index()->default(0);
                $table->string('session_id', 48)->index()->default('');    
                $table->string('ip_address', 12)->index()->default('');
                $table->bigInteger('stamp')->index()->default(0);
                $table->timestamps(); 
                $table->softDeletes();
            });
        }
        
        if( !$schema->hasTable('user_profiles') ) {
            $schema->create('user_profiles', function( $table ) {
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
        
        /**
        * Install groups
        */
        foreach($this->groups as $level=>$name) {
            $group        = new Group();
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
        
        $userAuth = new Contact();
        $userAuth->user_id = $user->id;
        $userAuth->type    = Contact::TYPE_EMAIL;
        $userAuth->value   = 'root@wasp.cms';
        $userAuth->default = 1;
        
        $userAuth->save();
        
        $userProfile = new Profile();
        $userProfile->user_id = $user->id;
        $userProfile->nicname = 'SuperUser';
        $userProfile->save();
        
        return 'Installation success!';
    }
    
    public function anyDeinstall()
    {
        $schema = $database->connection()->getSchemaBuilder();
        
        $schema->dropIfExists('users');
        $schema->dropIfExists('user_auths');
        $schema->dropIfExists('user_sessions');
        $schema->dropIfExists('user_profiles');
        $schema->dropIfExists('user_groups');

        return 'Deinstallation success!';
    }
}
  

