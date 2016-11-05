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

    // Seed PageTemplates
    // 
    $basicTemplate = new PageTemplate(['name' => 'basic', 'display_name' => 'Basic Page']);
    $basicTemplate->save();

    $contactTemplate = new PageTemplate(['name' => 'contact', 'display_name' => 'Contact Page']);
    $contactTemplate->save();

    $homeTemplate = new PageTemplate(['name' => 'home', 'display_name' => 'Home Page']);
    $homeTemplate->save();

    $loginPageTemplate = new PageTemplate(['name' => 'login', 'display_name' => 'Login Page']);
    $loginPageTemplate->save();

    $paymentPageTemplate = new PageTemplate(['name' => 'payment', 'display_name' => 'Payment Page']);
    $paymentPageTemplate->save();

    // Seed minimal Pages
    // 
    $homePage = new Page([
      'name' => 'Home',
      'full_path' => '/home',
      'slug' => 'home',
      'in_menu' => false,
      'deletable' => false,
      'draft' => false,
      'position' => 1
    ]);
    
    $aboutPage = new Page([
      'name' => 'About',
      'full_path' => '/about',
      'slug' => 'about',
      'in_menu' => true,
      'deletable' => false,
      'draft' => false,
      'position' => 2
    ]);

    $contactPage = new Page([
      'name' => 'Contact Us',
      'full_path' => '/contact',
      'slug' => 'contact',
      'in_menu' => true,
      'deletable' => false,
      'draft' => false,
      'position' => 3
    ]);

    $loginPage = new Page([
      'name' => 'Login',
      'full_path' => '/login',
      'slug' => 'login',
      'in_menu' => true,
      'deletable' => false,
      'draft' => false,
      'position' => 4
    ]);

    $donationPage = new Page([
      'name' => 'Donate',
      'full_path' => '/donate',
      'slug' => 'donate',
      'in_menu' => true,
      'deletable' => false,
      'draft' => false,
      'position' => 5
    ]);

    $homePage->save();
    $aboutPage->save();
    $contactPage->save();
    $loginPage->save();
    $donationPage->save();

    // Assign tempaltes and resave.
    $homePage->template()->associate($basicTemplate);
    $aboutPage->template()->associate($contactTemplate);
    $contactPage->template()->associate($homeTemplate);
    $loginPage->template()->associate($loginPageTemplate);
    $donationPage->template()->associate($paymentPageTemplate);

    $homePage->save();
    $aboutPage->save();
    $contactPage->save();
    $loginPage->save();
    $donationPage->save();
  }
}

      