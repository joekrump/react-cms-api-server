<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;

class UserTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array
   *
   * @return array
   */
  public function transform(User $user)
  {
    $role = $user->roles()->first();
    return [
      'id'        => (int) $user->id,
      'name'      => $user->name,
      'email'     => $user->email,
      'primary'   => $user->name,
      'secondary' => $user->email,
      'deletable' => true,
      'role'      => ['id' => $role->id, 'name' => $role->name, 'display_name' => $role->display_name]
    ];
  }
}