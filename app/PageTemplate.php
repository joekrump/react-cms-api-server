<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PageTemplate extends Model
{
  protected $fillable = [
    'name',
    'display_name',
  ];

  public function pages() {
    return $this->hasMany('App\Page', 'template_id', 'id');
  }
}
