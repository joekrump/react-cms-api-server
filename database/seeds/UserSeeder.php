<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Role;

class UserSeeder extends Seeder
{
  /**
   * Run the User seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table(Config::get('auth.table'))->delete();


    $adminRole = Role::where('name', 'admin')->first();
    $basicRole = Role::where('name', 'basic')->first();

    // Make an Admin User
    //
    $adminUser = new User();
    $adminUser->name = 'admin';
    $adminUser->email = 'admin@test.com';
    $adminUser->password = bcrypt('testing');
    $adminUser->save();
    $adminUser->roles()->attach($adminRole);

    // Make some Basic Users
    // 
    factory(App\User::class, 10)->create()->each(function($u) {
      $u->roles()->attach($basicRole);
    })
  }
}

      