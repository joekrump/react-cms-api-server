<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Permission;
use App\Role;
use App\User;
use App\Widget;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

      Model::unguard();
      
      DB::table('widgets')->delete();
      DB::table('role_user')->delete();
      DB::table(Config::get('auth.table'))->delete();
      DB::table('roles')->delete();
      DB::table('permissions')->delete();
      DB::table('permission_role')->delete();



      $activeUserWidget = Widget::create([
        'name' => 'Active Users', 
        'row' => 1,
        'col' => 1,
        'size' => 1,
        'widget_name' => 'ActiveUsersWidget'
      ]);
      
      $adminRole = Role::create(['name' => 'admin']);
      $basicRole = Role::create(['name' => 'basic']);

      $manageUserPermission = Permission::create(['name' => 'users']);
      $manageUserAccountPermission = Permission::create(['name' => 'user-account']);

      // Admin can manage their account and all users.
      // 
      $adminRole->attachPermission($manageUserPermission);
      $adminRole->attachPermission($manageUserAccountPermission);

      // Basic can manage their own account only.
      // 
      $basicRole->attachPermission($manageUserAccountPermission);

      // Make an Admin User
      // NOTE: User::create will hash the password
      $adminUser = User::create([
        'name' => 'admin',
        'email' => 'admin@test.com',
        'password' => 'testing'
      ]);

      $adminUser->roles()->attach($adminRole);


      // Make some Bsic Users
      // 
      $faker = Faker\Factory::create();
      $limit = 10;
      $users = [];

      for ($i = 0; $i < $limit; $i++) {
        $basicUser = User::create([
          'name' => $faker->name,
          'email' => $faker->unique()->email,
          'password' => 'testing'
        ]);
        $basicUser->roles()->attach($basicRole);
      }

      Model::reguard();
    }
}
