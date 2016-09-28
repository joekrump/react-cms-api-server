<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;
use App\Permission;
use App\Transformers\PermissionTransformer;
use Validator;
use Dingo\Api\Exception\ValidationHttpException;

/**
 * Permission resource representation.
 * 
 * @Resource("Permission", uri="/permissions")
 */
class PermissionController extends Controller
{
  use Helpers;
  /**
   * Display a listing of the resource.
   *
   * @return Dingo\Api\Http\Response 
   */
  public function index()
  {
    $permissions = Permission::orderBy('position')->get();
    return $this->response->collection($permissions, new PermissionTransformer);
  }

  public function updateIndex(Request $request) {
    $nodesArray = $request->get('nodeArray');
    $node;
    if($nodesArray) {
      $numNodes = count($nodesArray);
      // Note: first entry is being skipped
      for($i = 1; $i < $numNodes; $i++) {
        $node = $nodesArray[$i];
        Permission::where('id', $node['model_id'])->update(['position' => $i]);
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
      'name' => 'required|max:100|unique:permissions,name',
      'display_name' => 'required|max:100|unique:permissions,display_name',
      'description' => 'required|max:255'
      ]);


    if ($validator->fails()) {
      throw new \Dingo\Api\Exception\StoreResourceFailedException('Could not create new Permission.', $validator->errors());
    }


    $permission = new Permission($request->only(['name', 'display_name', 'description']));

    if($permission->save())
      return $this->response->item($permission, new PermissionTransformer)->setStatusCode(200);
    else
      return $this->response->errorBadRequest('Could Not Create Permission');
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    if($permission = Permission::find($id)){
      return $this->response->item($permission, new PermissionTransformer)->setStatusCode(200);
    } 
    return  $this->response->errorNotFound('Could Not Find details for Permission with id=' . $id);
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
    $permission = Permission::find($id);
    if(!$permission) {
      return $this->response->errorNotFound('Could Not Find Permission with id=' . $id);
    }

    $acceptedInput = $request->only(['name', 'display_name', 'description']);
    
    $validator = Validator::make($acceptedInput, [
      'name' => 'max:100|unique:permissions,name,' . $id,
      'display_name' => 'max:100', 
      'description' => 'max:255'
      ]);

    if ($validator->fails()) {
      throw new \Dingo\Api\Exception\UpdateResourceFailedException('Could not update the permission.', $validator->errors());
    }

    if($request->has('name')){
      $permission->name = $request->get('name');
    }
    if($request->has('display_name')){
      $permission->display_name = $request->get('display_name');
    }
    if($request->has('description')){
      $permission->description = $request->get('description');
    }

    if($permission->save())
      return $this->response->item($permission, new PermissionTransformer)->setStatusCode(200);
    else
      return $this->response->errorBadRequest('Could not Update Permission with id=' . $id);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    if($permission = Permission::find($id)) {
      if($permission->delete())
        return $this->response->noContent()->setStatusCode(200);
      else
        return $this->response->errorBadRequest('Could Note Remove the Permission with id=' . $id);
    }
    return $this->response->errorNotFound('Could not Find Permission to remove with an id=' . $id);
  }
}
