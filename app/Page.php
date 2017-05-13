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


  public function savePageContent($page_content, $maxLength = 21000) {
    if($page_content && (strlen($page_content) > $maxLength)) {
      $content_chunks = str_split($page_content, $maxLength);
      $page_parts = [];

      foreach($content_chunks as $chunk) {
        $page_parts[] = new PagePart(['content' => $chunk]);
      }

      $this->parts()->saveMany($page_parts);
    } else if($page_content) {
      $this->parts()->save(new PagePart(['content' => $page_content]));
    }
  }

  private static function updateExistingContentChunks(
    $page,
    $content,
    $max_content_length
  ) {
    $existing_parts = $page->parts;
    $content_chunks = Page::splitContentIntoChunks($content, $max_content_length);
    $num_existing_parts = $existing_parts->count();
    $num_chunks = count($content_chunks);
    $additional_parts = [];
    $i = 0;

    foreach($content_chunks as $chunk) {
      if($i < $num_existing_parts) {
        $existing_parts[$i]['content'] = $chunk;
        $existing_parts[$i]->save();
        $i++;
      } else {
        $additional_parts[] = new PagePart(['content' => $chunk]);
      } 
    }

    Page::removeExtraParts(
      $num_existing_parts,
      $existing_parts,
      $num_chunks
    );

    if(count($additional_parts) > 0) {
      $page->parts()->saveMany($additional_parts);
    }
  }

  private static function removeExtraParts(
    $existing_part_count,
    $existing_page_parts,
    $num_chunks
  ) {
    // If there are few parts than previously, delete the extra parts.
    // 
    if($existing_part_count > $num_chunks) {
      $pagePartIds = [];
      $i = $num_chunks;

      for($j = $i; $j < $existing_part_count; $j++){
        $pagePartIds[] = $existing_page_parts[$j]->id;
      }
      PagePart::whereIn('id', $pagePartIds)->delete();
    }
  }
  private static function splitContentIntoChunks($content, $max_content_length) {
    return str_split($content, $max_content_length);
  }

  public function updatePageContent(
    $content,
    $max_content_length = 21000
  ) {

    if(strlen($content) > $max_content_length) {
      $new_parts = Page::updateExistingContentChunks($this, $content, $max_content_length);
    } else {
      $existing_part_count = $this->parts()->count();

      if($existing_part_count > 0) {
        $first_page_part = $this->parts->first();
        $first_page_part->update(['content' => $content]);

        if($existing_part_count > 1) {
          $other_part_ids = $this->parts()
            ->whereNotIn('id', [$first_page_part->id])
            ->lists('page_parts.id');
          PagePart::whereIn('id',$other_part_ids)->delete();
        }
      } else {
        $this->parts()->save(new PagePart(['content' => $content]));
      }
    }
  }
}
