<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Page;

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
          'editor_contents' => $page->contents(),
          'use_editor'      => true
          // 'secondary'   => $page->full_path
        ];
    }

}