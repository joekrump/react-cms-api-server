<?php

namespace App\Api\V1\Controllers;

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use JWTAuth;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Log;
use Dingo\Api\Routing\Helpers;

class UserController extends Controller
{
  use Helpers;

  public function index()
  {
      // NOTE: Original 
      // $currentUser = JWTAuth::parseToken()->authenticate();
      // return $currentUser
      //     ->books()
      //     ->orderBy('created_at', 'DESC')
      //     ->get()
      //     ->toArray();
      //     
      
      return response()->json(['auth'=>Auth::user(), 'users'=>User::all()]);
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

  public function createRole(Request $request){
      // Todo       
  }

  public function createPermission(Request $request){
      // Todo       
  }

  public function assignRole(Request $request){
       // Todo
  }

  public function attachPermission(Request $request){
      // Todo       
  }
}
