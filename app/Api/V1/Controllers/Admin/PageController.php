<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Page;
use App\PagePart;
use App\PageTemplate;
use Dingo\Api\Routing\Helpers;
use App\Transformers\PageTransformer;
use App\Transformers\PageListTransformer;
use App\Helpers\PageHelper;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

class PageController extends Controller
{
  use Helpers;

  public function index()
  {
    $pages = Page::orderBy('name', 'asc')->get();

    return $this->response->collection($pages, new PageListTransformer);
  }


  public function store(Request $request)
  { 
    $credentials = $request->only(['name', 'in_menu', 'draft', 'slug', 'template_id', 'parent_id']);

    $page = new Page($credentials);

    if(is_null($credentials['slug'])){
      $page->slug = str_slug($page->name);
    } else {
      $page->slug = $credentials['slug'];
    }

    if(is_null($credentials['in_menu'])){
      $page->in_menu = false;
    }

    if(is_null($credentials['draft'])){
      $page->draft = false;
    }

    $page->full_path = PageHelper::makeFullPath($page);
    $validator = Validator::make($page->getAttributes(), [
        'name' => 'required',
        'template_id' => 'required|integer|min:1',
        'full_path' => 'unique:pages'
    ]);

    if($validator->fails()) {
      if(empty($validator->errors()->get('full_path'))){
        throw new ValidationHttpException($validator->errors());
      } else {
        // If there is an error for full_path it must be because it isn't unique. Therefore create a new unique slug and build a new full_path.
        $page->slug = PageHelper::makeSlug($page->slug);
        $page->full_path = PageHelper::makeFullPath($page);
      }
    }
    
    if($page->save()){

      // Assign content for the page.
      $page_content = $request->get('content');
      // If the content is longer than 21000 characters then split it amongst multiple page parts to
      // ensure content isn't trucated
      // 
      if($page_content && (strlen($page_content) > 21000)) {
        $content_chunks = str_split($page_content, 21000);
        $page_parts = [];
        foreach($content_chunks as $chunk) {
          $page_parts[] = new PagePart(['content' => $chunk]);
        }
        $page->parts()->saveMany($page_parts);
      } else if($page_content) {
        $page->parts()->save(new PagePart(['content' => $page_content]));
      } else {
        // no content in the page. 
      }

      return $this->response->item($page, new PageTransformer)->setStatusCode(200);
    } else {
      return $this->response->error('Error, could not create the page', 500);
    }
  }

  public function show($id)
  {

    $page = Page::find($id);

    if($page){

      return $this->response->item($page, new PageTransformer)->setStatusCode(200);
    } 
    return  $this->response->errorNotFound('Could Not Find details for Page with id=' . $id);
  }

  public function update(Request $request, $id)
  {
    $page = Page::find($id);
    if(!$page)
      throw new NotFoundHttpException;

    $specialFields = $request->only(['slug', 'template_id', 'parent_id']);
    $basicFields = $request->only(['name', 'in_menu', 'draft']);
    
    if(!is_null($specialFields['slug']) && ($specialFields['slug'] != $page->slug)){
      $page->slug = $specialFields['slug'];
      $page->full_path = PageHelper::makeFullPath($page);
    }

    if(!is_null($specialFields['template_id'])){
      $page->template_id = $specialFields['template_id'];
    }

    $validator = Validator::make($page->getAttributes(), [
      'template_id' => 'required|integer|min:1',
      'full_path' => "unique:pages,full_path,{$page->id}"
    ]);

    if($validator->fails()) {
      if(empty($validator->errors()->get('full_path'))){
        throw new ValidationHttpException($validator->errors());
      } else {
        // If there is an error for full_path it must be because it isn't unique. Therefore create a new unique slug and build a new full_path.
        $page->slug = PageHelper::makeSlug($page->slug);
        $page->full_path = PageHelper::makeFullPath($page);
      }
    }

    $page->fill($basicFields);

    if($page->save()){
      // Assign content for the page.
      //
      if(!is_null($specialFields['parent_id']) && $specialFields['parent_id'] != $page->parent_id) {
        // TODO: assign a parent page.
        $parent = Page::find($specialFields['parent_id']);
        $parent->children()->save($page)

      }

      $page_content = $request->get('content');

      if($page_content){
        // If the content is longer than 21000 characters then split it amongst multiple page parts to
        // ensure content isn't trucated
        // 
        if(strlen($page_content) > 21000) {
          $content_chunks = str_split($page_content, 21000);
          $existing_page_parts = $page->parts;
          $num_existing_parts = $existing_page_parts->count();
          $num_chunks = count($content_chunks);

          $page_parts = [];
          $i = 0;
          foreach($content_chunks as $chunk) {
            if($num_existing_parts > ($i + 1)) {
              $existing_page_parts[$i]['content'] = $chunk;
              $existing_page_parts[$i]->save();
              $i++;
            } else {
              $page_parts[] = new PagePart(['content' => $chunk]);
            } 
          }

          // If there are few parts than previously, delete the extra parts.
          // 
          if($num_existing_parts > $num_chunks) {
            $part_ids_to_remove = [];

            return $this->response->array(['num_to_delete' => $num_existing_parts - $num_chunks])->setStatusCode(200);

            for($j = $i; $j < $num_existing_parts; $j++){
              $part_ids_to_remove[] = $num_existing_parts[$j]->id;
            }
            PagePart::whereIn('id',$part_ids_to_remove)->delete();
          }

          if(count($page_parts) > 0) {
            $page->parts()->saveMany($page_parts);
          }
          
        } else {
          $num_parts = $page->parts()->count();

          if($num_parts > 0) {
            $first_page_part = $page->parts->first();
            $first_page_part->content = $page_content;
            $first_page_part->save();
            if($num_parts > 1) {
              $other_part_ids = $page->parts()->whereNotIn('id', [$first_page_part->id])->lists('page_parts.id');
              PagePart::whereIn('id',$other_part_ids)->delete();
            }
          } else {
            // If somehow there was no content for the page yet, create a new PagePart with content for the page.
            // 
            $page->parts()->save(new PagePart(['content' => $page_content]));
          }
        }
      }

      return $this->response->item($page, new PageTransformer)->setStatusCode(200);
    } else {
      return $this->response->error('could_not_update_page', 500);
    }
  }

  public function destroy($id)
  {

    $page = Page::find($id);

    if($page && $page->deleteable) {

      $page->parts()->delete();
      if($page->delete())
        return $this->response->noContent()->setStatusCode(200);
      else
        return $this->response->errorBadRequest('Could Note Remove the Page with id=' . $id);
    }
    if(!$page->deleteable) {
      return $this->response->errorBadRequest('Could Note Remove the Page with id=' . $id . ' It is not allowed to be deleted.');
    }
    return $this->response->errorNotFound('Could not Find Page to remove with an id=' . $id);
  }
}
