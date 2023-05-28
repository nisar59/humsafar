<?php

use Illuminate\Support\Facades\Route;

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

Route::group(['prefix'=>'regions','middleware' => ['permission:regions.view']],function(){
	Route::get('/', 'RegionsController@index');
});

Route::group(['prefix'=>'regions','middleware' => ['permission:regions.add']],function(){
	Route::get('/create', 'RegionsController@create');
	Route::POST('/store', 'RegionsController@store');
	Route::get('/import', 'RegionsController@import');
	Route::get('/sample-export', 'RegionsController@exportsample');
	Route::POST('/import-store', 'RegionsController@importstore');

});
Route::group(['prefix'=>'regions','middleware' => ['permission:regions.edit']],function(){
	Route::get('/edit/{id}', 'RegionsController@edit');
	Route::POST('/update/{id}', 'RegionsController@update');
	Route::get('/status/{id}', 'RegionsController@status');
});
Route::group(['prefix'=>'regions','middleware' => ['permission:regions.delete']],function(){
	Route::get('/destroy/{id}', 'RegionsController@destroy');
});

