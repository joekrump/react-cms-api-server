<?php

$api = app('Dingo\Api\Routing\Router');

// Routes without Auth requirement
// 
$api->group(['middleware' => 'cors', 'version' => 'v1'], function ($api) {

	// $api->get('auth/refresh', 'App\Api\V1\Controllers\AuthController@refresh_token');
	$api->post('login', 'App\Api\V1\Controllers\AuthController@login');
	$api->post('recovery', 'App\Api\V1\Controllers\AuthController@recovery');
	$api->post('reset', 'App\Api\V1\Controllers\AuthController@reset');
	$api->post('signup', 'App\Api\V1\Controllers\AuthController@signup')->middleware(['cors', 'signup_permission']);

	$api->post('stripe/make-payment', 'App\Api\V1\Controllers\PaymentController@process_payment');
	$api->get('users/count', 'App\Api\V1\Controllers\UserController@count');

	$api->get('page', 'App\Api\V1\Controllers\PageController@find');
});

// Routes requiring Auth
//
$api->group(['middleware' => ['api-auth'], 'version' => 'v1'], function ($api) {

	// Basic admin routes
	// 
	$api->post('logout', 'App\Api\V1\Controllers\AuthController@logout');
	$api->get('dashboard', 'App\Api\V1\Controllers\Admin\DashboardController@index');

	
	// User resource routes (Includes Role and Permission routes)
	// 
	$api->group(['middleware' => ['ability:admin,users']], function($api){

		$api->get('users', 'App\Api\V1\Controllers\Admin\UserController@index');
		$api->get('users/active', 'App\Api\V1\Controllers\Admin\UserController@activeUsers');
		$api->put('users/update-index', 'App\Api\V1\Controllers\Admin\UserController@updateIndex');
		$api->post('users', 'App\Api\V1\Controllers\Admin\UserController@store');

		$api->group(['middleware' => ['ability:admin,permissions']], function($api) {
			$api->post('attach-permissions', 'App\Api\V1\Controllers\Admin\RoleController@attachPermission');
		});

		$api->group(['middleware' => ['ability:admin,roles']], function($api) {
			$api->post('assign-role', 'App\Api\V1\Controllers\Admin\UserController@assignRole');
		});
	});

	$api->group(['middleware' => ['ability:admin,roles']], function($api){
		$api->put('roles/update-index', 'App\Api\V1\Controllers\Admin\RoleController@updateIndex');
		$api->resource('roles', 'App\Api\V1\Controllers\Admin\RoleController');
	});

	$api->group(['middleware' => ['ability:admin,permissions']], function($api){
		$api->put('permissions/update-index', 'App\Api\V1\Controllers\Admin\PermissionController@updateIndex');
		$api->resource('permissions', 'App\Api\V1\Controllers\Admin\PermissionController');
	});

	// Book resource routes
	// 
	$api->group(['middleware' => ['ability:admin,books']], function($api){
		$api->put('books/update-index', 'App\Api\V1\Controllers\Admin\BookController@updateIndex');
		$api->resource('books', 'App\Api\V1\Controllers\Admin\BookController');
	});
	
	// Page resoure routes
	// 
	$api->group(['middleware' => ['ability:admin,pages']], function($api){
		$api->put('pages/update-index', 'App\Api\V1\Controllers\Admin\PageController@updateIndex');
		$api->resource('pages', 'App\Api\V1\Controllers\Admin\PageController');
		$api->get('page-templates', 'App\Api\V1\Controllers\Admin\PageTemplateController@index');
	});

	// Card resource routes
	$api->group(['middleware' => ['ability:admin,cards']], function($api){
		$api->put('cards/update-index', 'App\Api\V1\Controllers\Admin\CardController@updateIndex');
		$api->resource('cards', 'App\Api\V1\Controllers\Admin\CardController');
		// $api->get('card-templates', 'App\Api\V1\Controllers\Admin\CardTemplateController@index');
	});

	// Special routes having to do with Users.
	$api->group(['middleware' => ['user_clearance:admin,users|user_profile']], function($api){
		$api->get('users/{id}', 'App\Api\V1\Controllers\Admin\UserController@show');
		$api->put('users/{id}', 'App\Api\V1\Controllers\Admin\UserController@update');
		$api->delete('users/{id}', 'App\Api\V1\Controllers\Admin\UserController@destroy');
	});
});

