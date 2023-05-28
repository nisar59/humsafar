<?php

use Illuminate\Support\Facades\Route;
use Modules\Packages\Http\Controllers\PackagesController;
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


Route::group(['prefix'=>'packages','middleware' => ['permission:packages.view']],function(){
	Route::get('/', 'PackagesController@index');
});

Route::group(['prefix'=>'packages','middleware' => ['permission:packages.add']],function(){
	Route::get('/create', 'PackagesController@create');
	Route::POST('/store', 'PackagesController@store');

});
Route::group(['prefix'=>'packages','middleware' => ['permission:packages.edit']],function(){
	Route::get('/edit/{id}', 'PackagesController@edit');
	Route::POST('/update/{id}', 'PackagesController@update');
	Route::get('/status/{id}', 'PackagesController@status');
});
Route::group(['prefix'=>'packages','middleware' => ['permission:packages.delete']],function(){
	Route::get('/destroy/{id}', 'PackagesController@destroy');
});
