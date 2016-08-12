<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Page;
use App\PagePart;
use App\PageTemplate;

class Page extends Seeder
{
  /**
   * Run the User seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('pages')->delete();
    DB::table('page_parts')->delete();
    DB::table('page_templates')->delete();

    $basicTemplate = new PageTemplate();
    $basicTemplate->name = 'basic';
    $basicTemplate->display_name='Basic Page';
    $basicTemplate->save();

    $indexTemplate = new PageTemplate();
    $indexTemplate->name = 'index';
    $indexTemplate->display_name='Index Page';
    $indexTemplate->save();

    $homeTemplate = new PageTemplate();
    $homeTemplate->name = 'home';
    $homeTemplate->display_name='Home Page';
    $homeTemplate->save();
  }
}

      