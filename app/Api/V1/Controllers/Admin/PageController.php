<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Page;
use Dingo\Api\Routing\Helpers;
use App\Transformers\PageTransformer;

class PageController extends Controller
{
  use Helpers;

  public function index()
  {
      $currentUser = JWTAuth::parseToken()->authenticate();
      $pages = Page::all();

      return $this->response->collection($pages, new PageTransformer);
  }


  public function store(Request $request)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $page = new Page;

      $page->title = $request->get('title');
      $page->author_name = $request->get('author_name');
      $page->pages_count = $request->get('pages_count');

      if($currentUser->pages()->save($page))
          return $this->response->item($page, new PageTransformer)->setStatusCode(200);
      else
          return $this->response->error('could_not_create_page', 500);
  }

  public function show($id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $page = $currentUser->pages()->find($id);

      if($page){
        return $this->response->item($page, new PageTransformer)->setStatusCode(200);
      } 
      return  $this->response->errorNotFound('Could Not Find details for Page with id=' . $id);
  }

  public function update(Request $request, $id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $page = $currentUser->pages()->find($id);
      if(!$page)
          throw new NotFoundHttpException;

      $page->fill($request->all());

      if($page->save())
          return $this->response->noContent();
      else
          return $this->response->error('could_not_update_page', 500);
  }

  public function destroy($id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $page = $currentUser->pages()->find($id);

      if($page) {
        if($page->delete())
          return $this->response->noContent()->setStatusCode(200);
        else
          return $this->response->errorBadRequest('Could Note Remove the Page with id=' . $id);
      }
      return $this->response->errorNotFound('Could not Find Page to remove with an id=' . $id);
  }
}
