<?php

namespace App\Helpers;
use App\Page;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PageHelper
{
  public static function makeFullPath(Page $page)
  {
    // TODO: get the depth of the page
    // 
    // if the depth is > 0 then get the parent page for this page
    // get the full_path for the parent page
    // append the slug of this page to the full_path of the parent page
    // return the full value.
  }

  public static function makeSlug($value_to_sluggify) {
    return SlugService::createSlug(Page::class, 'slug', $value_to_sluggify);
  }
}