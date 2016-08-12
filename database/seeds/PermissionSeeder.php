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

    Permission::create(['name' => 'users']);
    Permission::create(['name' => 'user-account']);
  }
}