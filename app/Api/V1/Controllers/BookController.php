<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use App\Book;
use Dingo\Api\Routing\Helpers;

class BookController extends Controller
{
  use Helpers;

  public function index()
  {
      $currentUser = JWTAuth::parseToken()->authenticate();
      return $currentUser
          ->books()
          ->orderBy('created_at', 'DESC')
          ->get()
          ->toArray();
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
}
