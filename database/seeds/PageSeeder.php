<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Page;
use App\PagePart;
use App\PageTemplate;

class PageSeeder extends Seeder
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

    $basicTemplate = new PageTemplate(['name' => 'basic', 'display_name' => 'Basic Page']);
    $basicTemplate->save();

    $indexTemplate = new PageTemplate(['name' => 'index', 'display_name' => 'Index Template']);
    $indexTemplate->save();

    $homeTemplate = new PageTemplate(['name' => 'home', 'display_name' => 'Home Tempalate']);
    $homeTemplate->save();

    $basicPage = new Page([
      'name' => 'Basic',
      'full_path' => '/basic',
      'in_menu' => false,
      'deleteable' => true,
      'draft' => false,
      'position' => 1
    ]);
    $indexPage = new Page([
      'name' => 'Index',
      'full_path' => '/index',
      'in_menu' => false,
      'deleteable' => true,
      'draft' => true,
      'position' => 2
    ]);
    $homePage = new Page([
      'name' => 'Home',
      'full_path' => '/',
      'in_menu' => true,
      'deleteable' => false,
      'draft' => false,
      'position' => 3
    ]);

    $basicPage->save();
    $indexPage->save();
    $homePage->save();

    $basicPageTitle = new PagePart([
      'title'=>'title', 
      'content' => '<h1>Basic</h1>', 
      'position' => 1
    ]);
    $indexPageTitle = new PagePart([
      'title'=>'title', 
      'content' => '<h1>Index</h1>', 
      'position' => 1
    ]);
    $homePageTitle = new PagePart([
      'title'=>'title', 
      'content' => '<h1>Home</h1>', 
      'position' => 1
    ]);

    $basicPageTitle->save();
    $indexPageTitle->save();
    $homePageTitle->save();

    $basicPage->parts()->save($basicPageTitle);
    $indexPage->parts()->save($indexPageTitle);
    $homePage->parts()->save($homePageTitle);

    $basicPage->template()->associate($basicTemplate);
    $indexPage->template()->associate($indexTemplate);
    $homePage->template()->associate($homeTemplate);

    $basicPage->save();
    $indexPage->save();
    $homePage->save();


  }
}

      