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
use Cache;
use Validator;

use App\Jobs\LogoutInactiveUser;

use App\Transformers\UserTransformer;


class UserController extends Controller
{
  use Helpers;


  public function index()
  {
      // $currentUser = JWTAuth::parseToken()->authenticate();
      
    return response()->json(['auth'=>Auth::user(), 'items'=>User::all(['id', 'name as primary', 'email as secondary'])]);
  }

  public function activeUsers(){
    $currentUser = JWTAuth::parseToken()->authenticate();

    $users = User::where([['id', '!=', $currentUser->id], ['logged_in', '=', 1]])
                        ->orderBy('name', 'ASC')
                        ->get(['email', 'name', 'id', 'logged_in']);

    $activeUsers = [];

    if($users){
      foreach ( $users as $user )
      {
        if(Cache::has('user-is-online-' . $user->id)){
          $activeUsers[] = $user;
        } else {
          // if they were not in the cache but had logged_in set to true, then set it to false.
          $this->dispatch(new LogoutInactiveUser($user));
        }
      } 
    }

    return compact('activeUsers');
  }

  /**
   * Method for handling a request to create and save a new User.
   * @param  Request $request - contains data for creating a new user
   * @return Dingo\Api\Http\Response - an api response.
   */
  public function store(Request $request)
  {
      // $currentUser = JWTAuth::parseToken()->authenticate();

      // $userParams = $request->only(['name', 'email', 'password']);

      $validator = Validator::make($request->only(['name', 'email', 'password']), [
          'name' => 'required|max:255|alpha_spaces',
          'email' => 'required|email|unique:users',
          'password' => 'required|min:7'
      ]);


      if ($validator->fails()) {
        throw new \Dingo\Api\Exception\StoreResourceFailedException('Could not create new user.', $validator->errors());
      }


      $user = new User($request->only(['name', 'email', 'password']));

      if($user->save())
        return $this->response->item($user, new UserTransformer)->setStatusCode(200);
      else
          return $this->response->error('could_not_create_user', 500);
  }

    /**
   * Method for handling a request to update an existing user.
   * @param  Request $request - contains data for creating a new user
   * @return Dingo\Api\Http\Response - an api response.
   */
  public function update(Request $request){
    $currentUser = JWTAuth::parseToken()->authenticate();
    // TODO: add logic for updating a user.
  }

  public function createRole(Request $request){
    $role = new Role();
    $role->name = $request->input('name');
    $role->save();

    return response()->json("created");    
  }

  public function createPermission(Request $request){
    $viewUsers = new Permission();
    $viewUsers->name = $request->input('name');
    $viewUsers->save();

    return response()->json("created");      
  }

  public function assignRole(Request $request){
    $user = User::where('email', '=', $request->input('email'))->first();
    $role = Role::where('name', '=', $request->input('role'))->first();
    $user->roles()->attach($role->id);

    return response()->json("created");
  }

  public function attachPermission(Request $request){
    $role = Role::where('name', '=', $request->input('role'))->first();
    $permission = Permission::where('name', '=', $request->input('name'))->first();
    $role->attachPermission($permission);

    return response()->json("created");
  }
}
