<?php
	
$api = app('Dingo\Api\Routing\Router');

$api->group(['middleware' => ['api-auth'], 'version' => 'v1'], function ($api) {

	$api->post('auth/logout', 'App\Api\V1\Controllers\AuthController@logout');

	// resource creates all RESTful CRUD routes
	// 
	$api->resource('books', 'App\Api\V1\Controllers\BookController'); 

	$api->get('dashboard', 'App\Api\V1\Controllers\DashboardController@index');

	

	// the user has to be an admin or have the create-users permissions before they can access the routes in this group
	// 
	$api->group(['middleware' => ['ability:admin,users']], function($api){
		$api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup');
		// Route to create a new role
		$api->post('role', 'App\Api\V1\Controllers\UserController@createRole');
		// Route to create a new permission
		$api->post('permission', 'App\Api\V1\Controllers\UserController@createPermission');
		// Route to assign role to user
		$api->post('assign-role', 'App\Api\V1\Controllers\UserController@assignRole');
		// Route to attache permission to a role
		$api->post('attach-permission', 'App\Api\V1\Controllers\UserController@attachPermission');
		// get list of active users
		$api->get('users/active', 'App\Api\V1\Controllers\UserController@activeUsers');

		$api->post('user/create', 'App\Api\V1\Controllers\UserController@store');
	});

	// Routes accessible by User with admin role, or with a role that has 'manage-users' or 'manage-user-account' permission assigned to it.
	//
	$api->group(['middleware' => ['ability:admin,users|user-account']], function($api){
		// TODO: add edit user profile should be here.
		// 
		// get list of users
		$api->get('users', 'App\Api\V1\Controllers\UserController@index');
	});
});

$api->group(['middleware' => 'cors', 'version' => 'v1'], function ($api) {

	$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');
	$api->post('auth/recovery', 'App\Api\V1\Controllers\AuthController@recovery');
	$api->post('auth/reset', 'App\Api\V1\Controllers\AuthController@reset');
	$api->post('stripe/make-payment', 'App\Api\V1\Controllers\PaymentController@process_payment');
});
