<?php
	
	$api = app('Dingo\Api\Routing\Router');

	$api->group(['middleware' => 'api.auth', 'version' => 'v1'], function ($api) {
		// resource creates all RESTful CRUD routes
		$api->resource('books', 'App\Api\V1\Controllers\BookController'); 
	});

	$api->version('v1', function ($api) {

	$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login')->middleware('cors');
	$api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup')->middleware('cors');
	$api->post('auth/recovery', 'App\Api\V1\Controllers\AuthController@recovery')->middleware('cors');
	$api->post('auth/reset', 'App\Api\V1\Controllers\AuthController@reset')->middleware('cors');

	// example of protected route
	$api->get('protected', ['middleware' => ['auth-cors'], function () {		
		return \App\User::all();
  }]);

	
	// example of free route
	$api->get('free', ['middleware' => ['cors'], function() {
		return \App\User::all();
	}]);

});
