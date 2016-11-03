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
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
      //
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
      //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
      //
  }

  /**
   * Find a page based on the fullpath provided in a query param.
   * @param  Request $request [description]
   * @return [type]           [description]
   */
  public function by_path(Request $request)
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

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
      //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      //
  }
}
