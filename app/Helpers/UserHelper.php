<?php

namespace App\Helpers;
use App\User;


class UserHelper
{
  public static function getPermissionNames(User $user) {
    $permissions = $user->roles->map(function($role) {
      $permissions = $role->permissions->map(function($permission) {
        return strtolower($permission->name);
      });
      return $permissions;
    });
    return $permissions->flatten();
  }

  public static function getRoleNames(User $user) {
    $roleNames = $user->roles->map(function($role) {
      return strtolower($role->name);
    });
    return $roleNames->flatten();
  }

  public static function getMenuList(User $user) {
    $permissionNames = self::getPermissionNames($user);
    return $permissionNames;
  }
}