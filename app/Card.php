<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
  protected $fillable = [
    'front_content',
    'back_content',
    'template_id'
  ];
}
