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
    $pages = Page::with('children')
      ->where('parent_id', null)
      ->orderBy('depth', 'asc')
      ->orderBy('position', 'asc')
      ->orderBy('name', 'asc')
      ->get();

    return $this->response->collection($pages, new PageListTransformer);
  }

  public function store(Request $request)
  { 
    $credentials = $request->only((new Page)->getFillable());

    $page = new Page($credentials);
    $page->slug = PageHelper::makeSlug((is_null($credentials['slug']) ? str_slug($page->name) : $credentials['slug']));
    $page->full_path = PageHelper::makeFullPath($page, $page->parent_id ?: null);

    $validator = Validator::make($credentials, [
      'name' => 'required',
      'template_id' => 'required|integer|min:1',
      'full_path' => 'unique:pages'
    ]);
    
    try {
      $page_content = $request->get('content');

      if($page_content && (strlen($page_content) > 21000)) {
        $content_chunks = str_split($page_content, 21000);
        $page->summary = PageHelper::makeSummary($content_chunks[0]);
      } else if($page_content) {
        $page->summary = PageHelper::makeSummary($page_content);
      } else {
        $page->summary = PageHelper::makeSummary($page->name);
      }

      $page->save();
      $page->savePageContent($page_content);

      return $this->response->item($page, new PageTransformer)->setStatusCode(200);
    } catch (Exception $e) {
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

  public function updateIndex(Request $request) {
    $minimalArray = $request->get('minimalArray');
    $node;
    $nodesOrder = [];

    if($minimalArray) {
      $i = 0;
      foreach($minimalArray as $node) {

        if($node['parent_id'] == -1) {
          $node['parent_id'] = null;
        }

        $page = Page::findOrFail($node['id']);
        $page->full_path = PageHelper::makeFullPath($page, $node['parent_id']);
        $page->parent_id = $node['parent_id'];
        $page->position = $i++;
        $page->save();
      }

      $pages = Page::with('children')
        ->where('parent_id', null)
        ->orderBy('depth', 'asc')
        ->orderBy('position', 'asc')
        ->orderBy('name', 'asc')
        ->get();

      return $this->response->collection($pages, new PageListTransformer);
      // return $this->response->noContent()->setStatusCode(200);
    } else {
      return $this->response->error('Update Failed, no data received.', 401);
    }
  }

  public function update(Request $request, $id)
  {
    $page = Page::find($id);
    if(!$page){
      throw new NotFoundHttpException;
    }

    $specialFields = $request->only(['slug', 'template_id', 'parent_id']);
    $basicFields = $request->only(['name', 'in_menu', 'draft', 'show_title', 'summary', 'image_url']);
    
    if(!is_null($specialFields['slug']) && ($specialFields['slug'] != $page->slug)){
      $page->slug = $specialFields['slug'];
      $page->full_path = PageHelper::makeFullPath($page, $page->parent_id);
    }

    if(!is_null($specialFields['template_id'])){
      $page->template_id = $specialFields['template_id'];
    }

    $validator = Validator::make($page->getAttributes(), [
      'template_id' => 'required|integer|min:1',
      'full_path' => "unique:pages,full_path,{$page->id}"
    ]);

    // Make sure were are not trying to save empty or null values which might overwrite existing
    // values.
    foreach ($basicFields as $attr_name => $value) {
      if(!is_null($value) && strlen($value)){
        $page[$attr_name] = trim(strip_tags($value));
      } else if(!is_null($value)) {
        $page[$attr_name] = $value;
      }
    }

    if(!is_null($specialFields['parent_id']) && $specialFields['parent_id'] != $page->parent_id) {
      // Assign this page to its parent.
      $parent = Page::find($specialFields['parent_id']);
      $parent->children()->save($page);
    }

    if($page->save()){

      $page_content = $request->get('content');

      if($page_content){
        $page->updatePageContent($page_content);
      }

      return $this->response->item($page, new PageTransformer)->setStatusCode(200);
    } else {
      return $this->response->error('Could not update page', 500);
    }
  }

  public function destroy($id)
  {

    $page = Page::find($id);

    if($page && $page->deletable) {

      $children = $page->children;
      $parent_id = $page->parent_id;

      foreach($children as $child) {
        $child->parent_id = $parent_id;
        $child->save();
      }

      $page->parts()->delete();

      if($page->delete()) {
        if(count($children) > 0 ){
          foreach($children as $child) {
            $child->full_path = PageHelper::makeFullPath($child, $parent_id);
            $child->save();
          }
        }
        return $this->response->noContent()->setStatusCode(200);
      } else {
        return $this->response->errorBadRequest("Could Note Remove the Page with id={$id}");
      }
    }
    if(!$page->deletable) {
      return $this->response->errorBadRequest("Could Note Remove the Page with id={$id}"
        . " It is not allowed to be deleted.");
    }
    return $this->response->errorNotFound("Could not Find Page to remove with an id={$id}");
  }
}
