<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Page;

class PageListTransformer extends TransformerAbstract
{
  /**
   * Turn the page into an associative array with nested children
   * if there are any.
   *
   * @return array
   */
  public function transform(Page $page)
  {
    if($page->children->count() > 0) {
      $page_with_children = $this->getDefaultFields($page);
      
      $childPages = $page->children()
        ->orderBy('depth', 'asc')
        ->orderBy('position', 'asc')
        ->orderBy('name', 'asc')->get();
      
      foreach ($childPages as $key => $value) {
         $page_with_children['children'][] = $this->transform($value);
      }
      return $page_with_children;
    } else {
      return $this->getDefaultFields($page);
    }
  }

  public function getDefaultFields(Page $page) {
    return [
      'id'          => $page->id,
      'deletable'   => $page->deletable,
      'previewPath' => $page->full_path,
      'depth'       => $page->depth,
      'draft'       => $page->draft,
      'primary'     => $page->name,
      'secondary'   => $page->full_path,
      'children'    => [],
      'unmovable'   => ($page->full_path == '/home'),
      'denyNested'  => ($page->full_path == '/home')
    ];
  }
}