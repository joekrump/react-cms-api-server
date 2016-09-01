<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Page;
use App\PagePart;
use App\PageTemplate;
use Dingo\Api\Routing\Helpers;
use App\Transformers\PageTransformer;

class PageController extends Controller
{
  use Helpers;

  public function index()
  {
    $pages = Page::all();
    return $this->response->collection($pages, new PageTransformer);
  }


  public function store(Request $request)
  { 
    if(!PageTemplate::find($request->get('template_id'))) {
      return $this->response->error('Could not find a template with id specified', 500);
    }

    $page = new Page;

    // dd($request->except('contents'));
    $page->name         = $request->get('name');
    $page->full_path    = '/' . $page->name;
    $page->template_id  = $request->get('template_id');

    // TODO:
    // ADD METHOD TO MAKE full_path for Page.
    // 
    // 
    if($page->save()){

      // Assign content for the page.
      $page_content = $request->get('contents');
        // If the content is longer than 21000 characters then split it amongst multiple page parts to
        // ensure content isn't trucated
        // 
      if($page_content && (strlen($page_content) > 21000)) {
        $content_chunks = str_split($page_content);
        $page_parts = [];
        foreach($content_chunks as $chunk) {
          $page_parts[] = new PagePart(['content' => $chunk]);
        }
        $page->parts()->saveMany($page_parts);
      } else {
        $page->parts()->save(new PagePart(['content' => $page_content]));
      }

      return $this->response->item($page, new PageTransformer)->setStatusCode(200);
    } else {
      return $this->response->error('could_not_create_page', 500);
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

    $page->fill($request->except('contents'));

    if($page->save()){
      // Assign content for the page.
      $page_content = $request->get('contents');
      
      // If the content is longer than 21000 characters then split it amongst multiple page parts to
      // ensure content isn't trucated
      // 
      if($page_content && (strlen($page_content) > 21000)) {
        $content_chunks = str_split($page_content);
        $existing_page_parts = $page->parts;
        $num_existing_parts = $existing_page_parts->count();

        $page_parts = [];
        $i = 0;
        foreach($content_chunks as $chunk) {
          if($num_existing_parts > ($i + 1)) {
            $existing_page_parts[$i]['content'] = $chunk;
            $existing_page_parts[$i]->save();
          } else {
            $page_parts[] = new PagePart(['content' => $chunk]);
          } 
        }

        if($page_parts.length > 0) {
          $page->parts()->saveMany($page_parts);
        }
        
      } else {
        $num_parts = $page->parts()->count();
        $first_page_part = $page->parts()->first();
        $first_page_part->content = $page_content;
        $first_page_part->save();

        if($num_parts > 1) {
          $other_part_ids = $page->parts()->whereNotIn([$first_page_part->id])->lists('page_parts.id');
          PagePart::whereIn($other_part_ids)->delete();
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
    $page->parts()->delete();

    if($page) {
      if($page->delete())
        return $this->response->noContent()->setStatusCode(200);
      else
        return $this->response->errorBadRequest('Could Note Remove the Page with id=' . $id);
    }
    return $this->response->errorNotFound('Could not Find Page to remove with an id=' . $id);
  }
}
