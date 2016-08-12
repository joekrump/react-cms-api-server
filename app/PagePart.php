<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PagePart extends Model
{ 
  protected $fillable = [
    'title',
    'content',
    'position'
  ];

  public function page() {
    return $this->belongsTo('App\Page');
  }
}
