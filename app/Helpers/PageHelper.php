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

  public static function makeSummary($content, $n = 200, $end_char = '&#8230;') {
    $content = strip_tags(preg_replace('/<img[^>]+>(<\/img>)?|<iframe.+?<\/iframe>/i', '', $content));

    if (strlen($content) < $n) {
      return $content;
    }

    $content = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $content));

    if (strlen($content) <= $n) {
      return $content;
    }

    $out = "";
    foreach (explode(' ', trim($content)) as $val) {
      $out .= $val.' ';

      if (strlen($out) >= $n) {
        $out = trim($out);
        return (strlen($out) == strlen($content)) ? $out : $out.$end_char;
      }
    }
  }
}