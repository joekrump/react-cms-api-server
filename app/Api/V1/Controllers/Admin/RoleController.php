<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Role;
use App\Http\Controllers\Controller;
use App\Transformers\RoleTransformer;
use Dingo\Api\Routing\Helpers;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

/**
 * Role resource representation.
 * 
 * @Resource("Role", uri="/roles")
 */
class RoleController extends Controller
{
  use Helpers;


  /**
   * Attach a permission to a Role
   * @param  Request $request 
   * @return Dingo\Api\Http\Response
   */
  public function attachPermission(Request $request){
    $permissionIds = $request->input('permissionIds');
    $role = Role::find($request->input('role_id'));
    if($permissionIds) {
      if($role) {
        $role->permissions()->sync($permissionIds);
      } else {
        return $this->response->errorNotFound('Could Not Find details for Role with id=' . $request->input('role_id'));
      }
    } else {
      return $this->response->errorNotFound('Could Not Find details for Permission with id=' . $request->input('permission_id'));
    }
    
    return $this->response->item($role, new RoleTransformer)->setStatusCode(200);
  }


  /**
   * Display a listing of the resource.
   *
   * @return Dingo\Api\Http\Response 
   */
  public function index()
  {
    $roles = Role::orderBy('position')->get();
    return $this->response->collection($roles, new RoleTransformer);
  }

  public function updateIndex(Request $request) {
    $minimalArray = $request->get('minimalArray');

    if($minimalArray) {
      $i = 0;
      foreach($minimalArray as $node) {
        Role::where('id', $node['id'])->update(['position' => $i++]);
      }
      return $this->response->noContent()->setStatusCode(200);
    } else {
      return $this->response->error('Update Failed, no data received.', 401);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->only(['name', 'display_name', 'description']), [
      'name' => 'required|max:100|unique:roles,name',
      'display_name' => 'required|max:100|unique:roles,display_name',
      'description' => 'required|max:255'
      ]);


    if ($validator->fails()) {
      throw new \Dingo\Api\Exception\StoreResourceFailedException('Could not create new Role.', $validator->errors());
    }


    $role = new Role($request->only(['name', 'display_name', 'description']));

    if($role->save())
      return $this->response->item($role, new RoleTransformer)->setStatusCode(200);
    else
      return $this->response->errorBadRequest('Could Not Create Role');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    if($role = Role::with('permissions')->find($id)){
      return $this->response->item($role, new RoleTransformer)->setStatusCode(200);
    } 
    return  $this->response->errorNotFound('Could Not Find details for Role with id=' . $id);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {

    $role = Role::find($id);
    if(!$role) {
      return $this->response->errorNotFound('Could Not Find Role with id=' . $id);
    }

    $acceptedInput = $request->only(['name', 'display_name', 'description']);
    
    $validator = Validator::make($acceptedInput, [
      'name' => 'max:100|unique:roles,name,' . $id,
      'display_name' => 'max:100|unique:roles,display_name,' . $id,
      'description' => 'max:255'
      ]);

    if ($validator->fails()) {
      throw new \Dingo\Api\Exception\UpdateResourceFailedException('Could not update the role.', $validator->errors());
    }

    if($request->has('name')){
      $role->name = $request->get('name');
    }
    if($request->has('display_name')){
      $role->display_name = $request->get('display_name');
    }
    if($request->has('description')){
      $role->description = $request->get('description');
    }

    if($role->save())
      return $this->response->item($role, new RoleTransformer)->setStatusCode(200);
    else
      return $this->response->errorBadRequest('Could not Update Role with id=' . $id);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    if($role = Role::find($id)) {
      if($role->delete())
        return $this->response->noContent()->setStatusCode(200);
      else
        return $this->response->errorBadRequest('Could Note Remove the Role with id=' . $id);
    }
    return $this->response->errorNotFound('Could not Find Role to remove with an id=' . $id);
  }
}
