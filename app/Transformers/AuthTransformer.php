<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;
use App\Helpers\UserHelper;

class AuthTransformer extends TransformerAbstract
{
  /**
   * Turn this item object into a generic array
   *
   * @return array
   */
  public function transform(User $user)
  {
    return [
    'id'          => (int) $user->id,
    'name'        => $user->name,
    'email'       => $user->email,
    'primary'     => $user->name,
    'logged_in'   => $user->logged_in,
    'secondary'   => $user->email,
    'deletable'   => true,
    'roles'       => UserHelper::getPermissionNames($user),
    'permissions' => UserHelper::getRoleNames($user),
    'menuList'    => UserHelper::getMenuList($user)
    ];
  }
}