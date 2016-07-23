<?php
	
$api = app('Dingo\Api\Routing\Router');

$api->group(['middleware' => ['api.auth', 'cors'], 'version' => 'v1'], function ($api) {
	// resource creates all RESTful CRUD routes
	$api->resource('books', 'App\Api\V1\Controllers\BookController'); 

	$api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup');
	// Route to create a new role
	$api->post('role', 'App\Api\V1\Controllers\UserController@createRole');
	// Route to create a new permission
	$api->post('permission', 'App\Api\V1\Controllers\UserController@createPermission');
	// Route to assign role to user
	$api->post('assign-role', 'App\Api\V1\Controllers\UserController@assignRole');
	// Route to attache permission to a role
	$api->post('attach-permission', 'App\Api\V1\Controllers\UserController@attachPermission');
	// get list of users
	$api->get('users', 'App\Api\V1\Controllers\UserController@index');
	// example of protected route
	$api->get('protected', function () {		
		return \App\User::all();
  });
});

$api->group(['middleware' => 'cors', 'version' => 'v1'], function ($api) {

	$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');
	$api->post('auth/recovery', 'App\Api\V1\Controllers\AuthController@recovery');
	$api->post('auth/reset', 'App\Api\V1\Controllers\AuthController@reset');
});
