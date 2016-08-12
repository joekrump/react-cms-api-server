<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Role;
use App\Permission;

class RoleSeeder extends Seeder
{
  /**
   * Run the Role seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('role_user')->delete();
    DB::table('roles')->delete();

    $adminRole = Role::create(['name' => 'admin']);
    $basicRole = Role::create(['name' => 'basic']);

    $userAccountPermission = Permission::where('name', 'user-account')->first();
    $userPermission = Permission::where('name', 'users')->first();

    // Admin can manage their account and all users.
    // 
    $adminRole->attachPermission($userAccountPermission);
    $adminRole->attachPermission($userPermission);

    // Basic can manage their own account only.
    // 
    $basicRole->attachPermission($userAccountPermission);
  }
}