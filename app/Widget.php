<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    protected $fillable = [
        'row', 'col', 'size', 'on_dashboard', 'name'
    ];
}
