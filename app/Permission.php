<?php namespace App;

use Zizaco\Entrust\EntrustPermission;

class Permission extends EntrustPermission
{
  protected $visible = ['id', 'name'];

  protected $fillable = [
      'name', 'display_name', 'description',
  ];
}