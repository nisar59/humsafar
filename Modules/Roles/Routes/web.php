<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix'=>'roles','middleware' => ['permission:permissions.view']],function(){
	Route::get('/', 'RolesController@index');
});

Route::group(['prefix'=>'roles','middleware' => ['permission:permissions.add']],function(){
	Route::get('/create', 'RolesController@create');
	Route::POST('/store', 'RolesController@store');

});
Route::group(['prefix'=>'roles','middleware' => ['permission:permissions.edit']],function(){
	Route::get('/edit/{id}', 'RolesController@edit');
	Route::POST('/update/{id}', 'RolesController@update');
});
Route::group(['prefix'=>'roles','middleware' => ['permission:permissions.delete']],function(){
	Route::get('/destroy/{id}', 'RolesController@destroy');
});