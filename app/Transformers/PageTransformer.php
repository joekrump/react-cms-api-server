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
            'primary'     => $book->title,
            'secondary'   => $book->slug
        ];
    }

}