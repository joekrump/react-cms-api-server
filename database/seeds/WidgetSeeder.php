<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Widget;

class WidgetSeeder extends Seeder
{
  /**
   * Run the Widget seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('widgets')->delete();
      
    $activeUserWidget = Widget::create([
      'name' => 'Active Users', 
      'row' => 1,
      'col' => 1,
      'size' => 1,
      'component_name' => 'ActiveUsersWidget'
    ]);
  }
}

      