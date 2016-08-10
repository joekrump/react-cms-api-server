<?php

namespace App\Api\V1\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Role;

use App\Http\Controllers\Controller;

use App\Transformers\RoleTransformer;

use Dingo\Api\Routing\Helpers;

/**
 * Role resource representation.
 * 
 * @Resource("Role", uri="/roles")
 */
class RoleController extends Controller
{
    use Helpers;
    /**
     * Display a listing of the resource.
     *
     * @return Dingo\Api\Http\Response 
     */
    public function index()
    {
        $roles = Role::all();
        return $this->response->collection($roles, new RoleTransformer);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $role = new Role();
        $role->name = $request->input('name');
        $role->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($role = Role::find($id)){
          return $this->response->item($role, new RoleTransformer)->setStatusCode(200);
        } 
        return  $this->response->errorNotFound('Could Not Find details for Role with id=' . $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
