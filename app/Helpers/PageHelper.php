<?php

namespace App\Helpers;
use App\Page;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PageHelper
{
  public static function makeFullPath(Page $page, $parentId)
  {
    if(isset($parentId)){
      $parentPath = Page::findOrFail($parentId)->full_path;
      return "{$parentPath}/{$page->slug}";
    } else {
      return "/{$page->slug}";
    }
  }

  public static function makeSlug($value_to_sluggify) {
    return SlugService::createSlug(Page::class, 'slug', $value_to_sluggify);
  }
}