<?php
	
$api = app('Dingo\Api\Routing\Router');

$api->group(['middleware' => ['api.auth', 'cors'], 'version' => 'v1'], function ($api) {
	// resource creates all RESTful CRUD routes
	$api->resource('books', 'App\Api\V1\Controllers\BookController'); 


	// example of protected route
	$api->get('protected', function () {		
		return \App\User::all();
  });
});

$api->group(['middleware' => 'cors', 'version' => 'v1'], function ($api) {

	$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');
	$api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup');
	$api->post('auth/recovery', 'App\Api\V1\Controllers\AuthController@recovery');
	$api->post('auth/reset', 'App\Api\V1\Controllers\AuthController@reset');
});
