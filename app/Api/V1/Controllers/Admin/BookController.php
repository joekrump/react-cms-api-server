<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Book;
use Dingo\Api\Routing\Helpers;
use App\Transformers\BookTransformer;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

class BookController extends Controller
{
  use Helpers;

  public function index()
  {
      $currentUser = JWTAuth::parseToken()->authenticate();
      $books = $currentUser->books()
          ->orderBy('created_at', 'DESC')
          ->get();

      return $this->response->collection($books, new BookTransformer);
  }


  public function store(Request $request)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $book = new Book;

      $credentials = $request->only(['title', 'author_name', 'pages_count']);

      $validator = Validator::make($credentials, [
          'title' => 'required',
          'author_name' => 'required',
          'pages_count' => 'required|integer|min:1',
      ]);

      if($validator->fails()) {
          throw new ValidationHttpException($validator->errors());
      }

      if($currentUser->books()->save($book))
          return $this->response->item($book, new BookTransformer)->setStatusCode(200);
      else
          return $this->response->error('could_not_create_book', 500);
  }

  public function show($id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $book = $currentUser->books()->find($id);

      if($book){
        return $this->response->item($book, new BookTransformer)->setStatusCode(200);
      } 
      return  $this->response->errorNotFound('Could Not Find details for Book with id=' . $id);
  }

  public function update(Request $request, $id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $book = $currentUser->books()->find($id);
      if(!$book)
          throw new NotFoundHttpException;

      $book->fill($request->all());

      if($book->save())
          return $this->response->item($book, new BookTransformer)->setStatusCode(200);
      else
          return $this->response->error('could_not_update_book', 500);
  }

  public function destroy($id)
  {
      $currentUser = JWTAuth::parseToken()->authenticate();

      $book = $currentUser->books()->find($id);

      if($book) {
        if($book->delete())
          return $this->response->noContent()->setStatusCode(200);
        else
          return $this->response->errorBadRequest('Could Note Remove the Book with id=' . $id);
      }
      return $this->response->errorNotFound('Could not Find Book to remove with an id=' . $id);
  }
}
