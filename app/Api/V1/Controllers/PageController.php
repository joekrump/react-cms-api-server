<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Page;
use Dingo\Api\Routing\Helpers;
use App\Transformers\PageTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
  use Helpers;

  public function find(Request $request)
  {
    $pagePath = $request->get('fullpath');

    if(!$pagePath) {
     return $this->response->errorNotFound('Could not Find Page'); 
    } else {
      if($pagePath[0] != '/') {
        $pagePath = '/' . $pagePath;
      }
      if($pagePath == '/') {
        $pagePath = '/home';
      }
      try {
        $page = Page::where('full_path', $pagePath)->firstOrFail();
        return $this->response->item($page, new PageTransformer)->setStatusCode(200);
      } catch (ModelNotFoundException $e) {
        return $this->response->errorNotFound('Could not Find Page: ' . $pagePath);
      } catch (Exception $e){
        return $this->response->errorBadRequest($e);
      }
    }
  }
}
