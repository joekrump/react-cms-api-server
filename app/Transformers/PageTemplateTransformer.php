<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\PageTemplate;

class PageTemplateTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array
   *
   * @return array
   */
  public function transform(PageTemplate $page_template)
  {

    return [
      'id'           => $page_template->id,
      'display_name' => $page_template->display_name
    ];
  }
}