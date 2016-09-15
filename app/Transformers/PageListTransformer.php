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

      return [
        'id'              => $page->id,
        'in_menu'         => $page->in_menu,
        'position'        => $page->position,
        'deleteable'      => $page->deleteable,
        'draft'           => $page->draft,
        'template_id'     => $page->template_id,
        'primary'         => $page->name,
        'use_editor'      => true
        // 'secondary'   => $page->full_path
      ];
  }
}