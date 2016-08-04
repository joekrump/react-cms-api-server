<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\User;
use App\Widget;
use Dingo\Api\Routing\Helpers;
use Cache;

class DashboardController extends Controller
{
  use Helpers;

  public function index()
  {
    $currentUser = JWTAuth::parseToken()->authenticate();

    $users = User::where([['id', '!=', $currentUser->id], ['logged_in', '=', 1]])
                        ->orderBy('name', 'ASC')
                        ->get(['email', 'name', 'id', 'logged_in']);

    $activeUsers = [];

    $widgets = Widget::where('on_dashboard', 1)->orderBy('row', 'asc')->orderBy('col', 'asc')->get();

    if($users){
      foreach ( $users as $user )
      {
        if(Cache::has('user-is-online-' . $user->id)){
          $activeUsers[] = $user;
        }
      } 
    }
    

    return ['users' => $activeUsers, 'widgets' => $widgets];
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
