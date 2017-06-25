<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Page;
use App\PageTemplate;

class PageTransformer extends TransformerAbstract
{

  public function getDefaultFields(Page $page) {
    return [
      'id'              => $page->id,
      'name'            => $page->name,
      'full_path'       => $page->full_path,
      'slug'            => $page->slug,
      'in_menu'         => $page->in_menu,
      'depth'           => $page->depth,
      'position'        => $page->position,
      'deletable'       => $page->deletable,
      'draft'           => $page->draft,
      'template_id'     => $page->template_id,
      'primary'         => $page->name,
      'use_editor'      => true,
      'content'         => $page->contents(),
      'summary'         => $page->summary,
      'show_title'      => $page->show_title,
      'image_url'       => $page->image_url,
      'templates'       => PageTemplate::orderBy('display_name', 'asc')->get(['display_name as displayName', 'id']),
      'children'        => [],
    ];
  }

  public function getBasicFields(Page $page) {
    return [
      'id'              => $page->id,
      'name'            => $page->name,
      'full_path'       => $page->full_path,
      'summary'         => $page->summary,
      'image_url'       => $page->image_url,
    ];
  }

  public function transform(Page $page, $isChildPage = false)
  {
    if($page->children()->where('draft', false)->count() > 0 && !$isChildPage) {
      $page_with_children = $this->getDefaultFields($page);
      
      $childPages = $page->children()
        ->where('draft', false)
        ->orderBy('depth', 'asc')
        ->orderBy('position', 'asc')
        ->orderBy('name', 'asc')->get();
      
      foreach ($childPages as $key => $value) {
         $page_with_children['children'][] = $this->transform($value, true);
      }
      return $page_with_children;
    } else if($isChildPage) {
      return $this->getBasicFields($page);
    } else {
      return $this->getDefaultFields($page);
    }
  }
}