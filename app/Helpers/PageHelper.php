<?php

namespace App\Helpers;
use App\Page;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PageHelper
{
  public static function makeFullPath(Page $page)
  {
    if(!empty($page->depth) && $page->depth > 0){
      $parentPath = $page->parent()->get(['full_path']);
      return "{$parentPath}/{$page->slug}";
    } else {
      return "/{$page->slug}";
    }
  }

  public static function makeSlug($value_to_sluggify) {
    return SlugService::createSlug(Page::class, 'slug', $value_to_sluggify);
  }
}