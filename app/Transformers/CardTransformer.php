<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Card;

class CardTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array
   *
   * @return array
   */
  public function transform(Card $card)
  {
    $substrLength = 20;
    return [
    'id'            => $card->id,
    'front_content' => $card->front_content,
    'back_content'  => $card->back_content,
    'template_id'   => $card->template_id,
    'primary'       => substr(strip_tags($card->front_content), 0, $substrLength),
    'secondary'     => substr(strip_tags($card->back_content), 0, $substrLength)
    ];
  }

}