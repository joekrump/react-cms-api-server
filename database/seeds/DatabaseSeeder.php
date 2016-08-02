<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Permission;
use App\Role;
use App\User;

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

      $adminRole = new Role(['name' => 'admin'])->save();
      $basicRole = new Role(['name' => 'basic'])->save();

      $manageUserPermission new Permission(['name' => 'manage-users'])->save();

      $adminRole->attachPermission($manageUserPermission);

      $adminUser = User::create([
        'name' => 'admin',
        'email' => 'admin@test.com',
        'password' => Hash::make('testing')
      ]);

      $adminUser->roles()->attach($adminRole);

      DB::table(Config::get('auth.table'))->delete(); // clear table, likely 'Users'

      // Make some basic users with no Roles
      $faker = Faker\Factory::create();
      $limit = 10;
      $users = [];

      for ($i = 0; $i < $limit; $i++) {
        $basicUser = User::create([
          'name' => $faker->name,
          'email' => $faker->unique()->email,
          'password' => Hash::make('testing')
        ]);
        $basicUser->roles()->attach($basicRole);
      }

      Model::reguard();
    }
}
