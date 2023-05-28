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

Route::group(['prefix'=>'trash','middleware' => ['permission:trash.view']],function(){
    Route::get('/', 'TrashController@index');
    Route::get('/show/{module}', 'TrashController@show');
});

Route::group(['prefix'=>'trash','middleware' => ['permission:trash.edit']],function(){
    Route::get('/restore/{module}/{id}', 'TrashController@restore');
});
Route::group(['prefix'=>'trash','middleware' => ['permission:trash.delete']],function(){
    Route::get('/destroy/{module}/{id}', 'TrashController@destroy');
});