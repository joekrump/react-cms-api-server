<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
      'full_path',
      'in_menu',
      'deleteable',
      'draft',
      'position'
    ];

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
}