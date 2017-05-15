<?php

namespace App\Helpers;
use App\Page;
use \Cviebrock\EloquentSluggable\Services\SlugService;

class PageHelper
{
  public static function makeFullPath(Page $page, $parentId) {
    if(isset($parentId) && !is_null($parentId)) {
      $parentPath = Page::findOrFail($parentId)->full_path;
      return "{$parentPath}/{$page->slug}";
    } else {
      return "/{$page->slug}";
    }
  }

  public static function makeSlug($value_to_sluggify) {
    return SlugService::createSlug(Page::class, 'slug', $value_to_sluggify);
  }

  public static function makeSummary($content, $page, $n = 200) {

    if(!$content) {
      $content = $page->name; // default to page name if no page content provided
    }

    $content = strip_tags(preg_replace('/<img[^>]+>(<\/img>)?|<iframe.+?<\/iframe>/i', '', $content));
    $content = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $content));

    if (strlen($content) < $n) {
      return trim($content);
    } else {
      return substr($content, 0, $n) . "...";
    }
  }
}
