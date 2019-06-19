<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

	// Inicio de sesión.
	Route::post('/oauth/token', 'PassportController@login');

	// Rutas protegidas.
	Route::group(['middleware' => 'auth:api'], function() {

		// Registrar usuario.
		Route::post('/users', 'UserController@create');

		// Consultar usuario por ID
		Route::get("/users/{id}", "UserController@find")->where('id','[1-9][0-9]*');

		// Consutal todos los usuarios
		Route::get("/users", "UserController@findAll");

		// Elimina un usuario por ID
		Route::delete("/users/{id}", "UserController@delete")->where('id','[1-9][0-9]*');

		// Actualiza un usuario por ID
		Route::put("/users/{id}", "UserController@update")->where('id','[1-9][0-9]*');

		// Cerrar sesión.
		Route::delete('/oauth/token', 'PassportController@logout');

});
