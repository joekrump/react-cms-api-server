<?php

namespace App\Helpers;
use App\Page;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PageHelper
{
  public static function makeFullPath(Page $page)
  {

  }

  public static function makeSlug($value_to_sluggify) {
    return SlugService::createSlug(Post::class, 'slug', $value_to_sluggify);
  }
}