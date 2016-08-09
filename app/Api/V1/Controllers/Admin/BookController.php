<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Book;
use Dingo\Api\Routing\Helpers;

class BookController extends Controller
{
  use Helpers;

  public function index()
  {
      $currentUser = JWTAuth::parseToken()->authenticate();
      return ['items' => $currentUser
          ->books()
          ->orderBy('created_at', 'DESC')
          ->get(['id', 'title as primary', 'author_name as secondary'])];
  }


  public function store(Request $request)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $book = new Book;

      $book->title = $request->get('title');
      $book->author_name = $request->get('author_name');
      $book->pages_count = $request->get('pages_count');

      if($currentUser->books()->save($book))
          return $this->response->created();
      else
          return $this->response->error('could_not_create_book', 500);
  }

  public function show($id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $book = $currentUser->books()->find($id);

      if(!$book)
          throw new NotFoundHttpException; 

      return $book;
  }

  public function update(Request $request, $id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $book = $currentUser->books()->find($id);
      if(!$book)
          throw new NotFoundHttpException;

      $book->fill($request->all());

      if($book->save())
          return $this->response->noContent();
      else
          return $this->response->error('could_not_update_book', 500);
  }

  public function destroy($id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $book = $currentUser->books()->find($id);

      if(!$book)
          throw new NotFoundHttpException;

      if($book->delete())
          return $this->response->noContent();
      else
          return $this->response->error('could_not_delete_book', 500);
  }
}
