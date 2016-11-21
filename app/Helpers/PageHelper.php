<?php

namespace App\Helpers;
use App\Page;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PageHelper
{
  public static function makeFullPath(Page $page, $parentId)
  {
    if(isset($parentId) && !is_null($parentId)){
      $parentPath = Page::findOrFail($parentId)->full_path;
      return "{$parentPath}/{$page->slug}";
    } else {
      return "/{$page->slug}";
    }
  }

  public static function makeSlug($value_to_sluggify) {
    return SlugService::createSlug(Page::class, 'slug', $value_to_sluggify);
  }

  public static function makeSummary($content) {
    if (strlen($str) < $n) {
      return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));

    if (strlen($str) <= $n) {
      return $str;
    }

    $out = "";
    foreach (explode(' ', trim($str)) as $val) {
      $out .= $val.' ';

      if (strlen($out) >= $n) {
        $out = trim($out);
        return (strlen($out) == strlen($str)) ? $out : $out.$end_char;
      }
    }
  }
}