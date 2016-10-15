<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;
use App\Helpers\UserHelper;

class UserWidgetTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array
   *
   * @return array
   */
  public function transform(User $user)
  {
    return [
      'name'        => $user->name,
      'email'       => $user->email,
      'logged_in'   => $user->logged_in
    ];
  }
}