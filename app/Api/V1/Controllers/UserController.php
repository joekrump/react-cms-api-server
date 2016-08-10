<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{
  function count() {
    $count = User::all()->count();
    return response()->json(['count' => $count]);
  }
}