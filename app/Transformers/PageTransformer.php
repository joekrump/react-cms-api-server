<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Page;
use App\PageTemplate;

class PageTransformer extends TransformerAbstract
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
        'name'            => $page->name,
        'full_path'       => $page->full_path,
        'in_menu'         => $page->in_menu,
        'position'        => $page->position,
        'deleteable'      => $page->deleteable,
        'draft'           => $page->draft,
        'template_id'     => $page->template_id,
        'primary'         => $page->name,
        'use_editor'      => true,
        'content'         => $page->contents(),
        'templates'       => PageTemplate::orderBy('display_name', 'asc')->get(['display_name', 'id'])
        // 'secondary'   => $page->full_path
      ];
  }
}