<?php


$api = app('Dingo\Api\Routing\Router');

// Routes requiring Auth
//
$api->group(['middleware' => ['api-auth'], 'version' => 'v1'], function ($api) {

	$api->post('auth/logout', 'App\Api\V1\Controllers\AuthController@logout');

	$api->resource('books', 'App\Api\V1\Controllers\Admin\BookController'); 

	$api->get('dashboard', 'App\Api\V1\Controllers\Admin\DashboardController@index');

	// the user has to be an admin or have the create-users permissions before they can access the routes in this group
	// 
	$api->group(['middleware' => ['ability:admin,users']], function($api){
		$api->post('auth/signup', 'App\Api\V1\Controllers\AuthController@signup');
		// Route to create a new role
		$api->post('role', 'App\Api\V1\Controllers\Admin\UserController@createRole');
		// Route to create a new permission
		$api->post('permission', 'App\Api\V1\Controllers\Admin\UserController@createPermission');
		// Route to assign role to user
		$api->post('assign-role', 'App\Api\V1\Controllers\Admin\UserController@assignRole');

		// Route to attach permission to a role
		$api->post('attach-permission', 'App\Api\V1\Controllers\Admin\UserController@attachPermission');
		
		// get list of active users
		// 
		$api->get('users/active', 'App\Api\V1\Controllers\Admin\UserController@activeUsers');

		$api->post('user/create', 'App\Api\V1\Controllers\Admin\UserController@store');

		$api->get('users', 'App\Api\V1\Controllers\Admin\UserController@index');
	});

	$api->group(['middleware' => ['user_clearance:admin,users|user_profile']], function($api){
		$api->get('user/{id}', 'App\Api\V1\Controllers\Admin\UserController@details');
		$api->post('user/{id}/update', 'App\Api\V1\Controllers\Admin\UserController@update');
		$api->delete('user/{id}', 'App\Api\V1\Controllers\Admin\UserController@destroy');
	});
});


// Routes without Auth requirement
// 
$api->group(['middleware' => 'cors', 'version' => 'v1'], function ($api) {

	$api->post('auth/login', 'App\Api\V1\Controllers\AuthController@login');
	$api->post('auth/recovery', 'App\Api\V1\Controllers\AuthController@recovery');
	$api->post('auth/reset', 'App\Api\V1\Controllers\AuthController@reset');
	$api->post('stripe/make-payment', 'App\Api\V1\Controllers\PaymentController@process_payment');
});
