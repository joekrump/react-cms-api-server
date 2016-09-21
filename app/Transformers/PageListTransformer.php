<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Page;

class PageListTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array
   *
   * @return array
   */
  public function transform(Page $page)
  {
    if($page->children->count() > 0) {
      $page_with_children = $this->getDefaultFields($page);

      foreach ($page->children as $key => $value) {
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
      'in_menu'     => $page->in_menu,
      'position'    => $page->position,
      'depth'       => $page->depth,
      'deletable'   => $page->deletable,
      'draft'       => $page->draft,
      'template_id' => $page->template_id,
      'primary'     => $page->name,
      'use_editor'  => true,
      'secondary'   => $page->full_path
    ];
  }
}