<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Widget;
use Dingo\Api\Routing\Helpers;

class DashboardController extends Controller
{
  use Helpers;

  public function index()
  {
    // $currentUser = JWTAuth::parseToken()->authenticate();

    $widgets = Widget::where('on_dashboard', 1)->orderBy('row', 'asc')->orderBy('col', 'asc')->get();
    

    return compact('widgets');
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
