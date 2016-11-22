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

  private static function updateExistingContentChunks($page, $content_chunks, $existingPageParts, $existingPartCount) {
    
    $page_parts = [];
    $i = 0;

    foreach($content_chunks as $chunk) {
      if($existingPartCount > ($i + 1)) {
        $existingPageParts[$i]['content'] = $chunk;
        $existingPageParts[$i]->save();
        $i++;
      } else {
        $page_parts[] = new PagePart(['content' => $chunk]);
      } 
    }

    return $page_parts;
  }

  private static function removeExtraPageParts($existingPartCount, $existingPageParts, $num_chunks) {
    // If there are few parts than previously, delete the extra parts.
    // 
    if($existingPartCount > $num_chunks) {
      $pagePartIds = [];
      $i = $num_chunks;

      for($j = $i; $j < $existingPartCount; $j++){
        $pagePartIds[] = $existingPageParts[$j]->id;
      }
      PagePart::whereIn('id', $pagePartIds)->delete();
    }
  }

  public function updatePageContent($page_content, $maxLength = 21000) {

    if(strlen($page_content) > $maxLength) {
      $content_chunks = str_split($page_content, $maxLength);
      $existingPageParts = $page->parts;
      $existingPartCount = $existingPageParts->count();
      $num_chunks = count($content_chunks);
      $newParts = Page::updateExistingContentChunks($this, $content_chunks, $existingPageParts, $existingPartCount);

      Page::removeExtraPageParts();

      if(count($newParts) > 0) {
        $page->parts()->saveMany($newParts);
      }
      
    } else {
      $existingPartCount = $page->parts()->count();

      if($existingPartCount > 0) {
        $first_page_part = $page->parts->first();
        $first_page_part->update(['content' => $page_content]);

        if($existingPartCount > 1) {
          $other_part_ids = $page->parts()
            ->whereNotIn('id', [$first_page_part->id])
            ->lists('page_parts.id');
          PagePart::whereIn('id',$other_part_ids)->delete();
        }
      } else {
        $page->parts()->save(new PagePart(['content' => $page_content]));
      }
    }
  }
}
