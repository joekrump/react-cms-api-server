<?php

use Illuminate\Database\Seeder;

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

      DB::table(Config::get('auth.table'))->delete();

      $faker = Faker\Factory::create();
      $limit = 10;
      $users = [];

      for ($i = 0; $i < $limit; $i++) {
        User::create([
          'name' => $faker->name,
          'email' => $faker->unique()->email,
          'password' => Hash::make('testing')
        ]);
      }

      Model::reguard();
        // $this->call(UsersTableSeeder::class);
    }
}
