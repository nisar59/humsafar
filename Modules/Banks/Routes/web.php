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

Route::group(['prefix'=>'banks','middleware' => ['permission:banks.view']],function(){
	Route::get('/', 'BanksController@index');
});

Route::group(['prefix'=>'banks','middleware' => ['permission:banks.add']],function(){
	Route::get('/create', 'BanksController@create');
	Route::POST('/store', 'BanksController@store');
	Route::get('/import', 'BanksController@import');
	Route::get('/sample-export', 'BanksController@exportsample');
	Route::POST('/import-store', 'BanksController@importstore');

});
Route::group(['prefix'=>'banks','middleware' => ['permission:banks.edit']],function(){
	Route::get('/edit/{id}', 'BanksController@edit');
	Route::POST('/update/{id}', 'BanksController@update');
	Route::get('/status/{id}', 'BanksController@status');

});
Route::group(['prefix'=>'banks','middleware' => ['permission:banks.delete']],function(){
	Route::get('/destroy/{id}', 'BanksController@destroy');
});
