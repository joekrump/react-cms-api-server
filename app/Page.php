<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends Model
{
  use Sluggable;

  protected $fillable = [
    'in_menu',
    'name',
    'deletable',
    'depth',
    'draft',
    'slug',
    'position',
    'template_id'
  ];

  /**
   * Return the sluggable configuration array for this model.
   *
   * @return array
   */
  public function sluggable()
  {
      return [
          'slug' => [
              'source' => 'name'
          ]
      ];
  }

  public function children() {
    return $this->hasMany('App\Page', 'parent_id', 'id');
  }

  public function parent()
  {
      return $this->belongsTo('App\Page', 'parent_id');
  }

  public function template()
  {
      return $this->belongsTo('App\PageTemplate', 'template_id');
  }

  public function parts()
  {
      return $this->hasMany('App\PagePart');
  }

  public function contents() {
    $partsWithContent = '';

    foreach($this->parts as $part) {
      $partsWithContent .= $part->content;
    }

    return $partsWithContent;
  }
}
