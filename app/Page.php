<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends Model
{
  use Sluggable;

  protected $fillable = [
    'name',
    'in_menu',
    'slug',
    'draft',
    'template_id',
    'parent_id',
    'summary',
    'show_title',
    'image_url'
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


  public function savePageContent($page_content) {
    if($page_content && (strlen($page_content) > 21000)) {
      $content_chunks = str_split($page_content, 21000);
      $page_parts = [];

      foreach($content_chunks as $chunk) {
        $page_parts[] = new PagePart(['content' => $chunk]);
      }

      $this->parts()->saveMany($page_parts);
    } else if($page_content) {
      $this->parts()->save(new PagePart(['content' => $page_content]));
    }
  }
}
