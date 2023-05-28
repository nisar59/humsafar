<?php

use Illuminate\Support\Facades\Route;
use Modules\Branches\Http\Controllers\BranchesController;

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



Route::group(['prefix'=>'branches','middleware' => ['permission:branches.view']],function(){
	Route::get('/', 'BranchesController@index');
});

Route::group(['prefix'=>'branches','middleware' => ['permission:branches.add']],function(){
	Route::get('/create', 'BranchesController@create');
	Route::POST('/region-areas', 'BranchesController@regionAreas');
	Route::POST('/store', 'BranchesController@store');
	Route::get('/import', 'BranchesController@import');
	Route::get('/sample-export', 'BranchesController@exportsample');
	Route::POST('/import-store', 'BranchesController@importstore');

});

Route::group(['prefix'=>'branches','middleware' => ['permission:branches.edit']],function(){
	Route::get('/edit/{id}', 'BranchesController@edit');
	Route::POST('/update/{id}', 'BranchesController@update');
	Route::get('/load-edit-areas/{region_id}/{area_id}', 'BranchesController@loadEditAreas');
	Route::get('/status/{id}', 'BranchesController@status');
});
Route::group(['prefix'=>'branches','middleware' => ['permission:branches.delete']],function(){
	Route::get('/destroy/{id}', 'BranchesController@destroy');
});
