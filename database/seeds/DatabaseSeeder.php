<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $this->call(PermissionSeeder::class);
      $this->call(RoleSeeder::class);
      $this->call(UserSeeder::class);
      $this->call(WidgetSeeder::class);
      $this->call(PageSeeder::class);
    }
}
