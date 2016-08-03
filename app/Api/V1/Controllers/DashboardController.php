<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use Dingo\Api\Routing\Helpers;
use Cache;

class DashboardController extends Controller
{
  use Helpers;

  public function index()
  {
    // $currentUser = JWTAuth::parseToken()->authenticate();
    $users =  User::orderBy('logged_in', 'DESC')
                        ->orderBy('name', 'ASC')
                        ->get(['email', 'name', 'id', 'logged_in']);

    foreach ( $users as $user )
    {
      $user->logged_in = Cache::has('user-is-online-' . $user->id);
    } 

    return compact('users');
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
