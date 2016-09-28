<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Book;

class BookTransformer extends TransformerAbstract
{

    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform(Book $book)
    {
        return [
            'id'          => $book->id,
            'title'       => $book->title,
            'author_name' => $book->author_name,
            'pages_count' => $book->pages_count,
            'primary'     => $book->title,
            'secondary'   => $book->author_name,
            'deletable'   => true
        ];
    }

}