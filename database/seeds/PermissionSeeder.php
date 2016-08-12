<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Permission;

class PermissionSeeder extends Seeder
{
  /**
   * Run the Permission seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('permission_role')->delete();
    DB::table('permissions')->delete();

    Permission::create(['name' => 'users', 'display_name' => 'User Permissions', 'description' => 'Allows Read and Delete Access for Users']);
    Permission::create(['name' => 'user-account', 'display_name' => 'User Account Permissions', 'description' => 'Allow Write Access to Users']);
  }
}