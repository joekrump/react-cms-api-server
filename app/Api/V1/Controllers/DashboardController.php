<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use Dingo\Api\Routing\Helpers;

class DashboardController extends Controller
{
  use Helpers;

  public function index()
  {
      // $currentUser = JWTAuth::parseToken()->authenticate();
      $users = User::all();
      
      return [
        'users' => User::orderBy('logged_in', 'DESC')
                          ->orderBy('name', 'ASC')
                          ->get(['email', 'name', 'id', 'logged_in'])
      ];
  }


  public function store(Request $request)
  {

  }

  public function show($id)
  {
      // $currentUser = JWTAuth::parseToken()->authenticate();
  }

  public function update(Request $request, $id)
  {
      // $currentUser = JWTAuth::parseToken()->authenticate();
  }

  public function destroy($id)
  {
  }
}
