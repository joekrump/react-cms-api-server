<?php

namespace App\Helpers;
use App\Role;

class RoleHelper
{
  public static function getPermissionNames(Role $role) {
    $permissions = $role->permissions->map(function($permission) {
      return strtolower($permission->name);
    });
    return $permissions->flatten();
  }
}