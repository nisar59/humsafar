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

Route::group(['prefix'=>'logs','middleware' => ['permission:logs.view']],function(){
    Route::get('/', 'LogsController@index');
    Route::get('/show/{id}', 'LogsController@show');
});

Route::group(['prefix'=>'logs','middleware' => ['permission:logs.delete']],function(){
    Route::get('/destroy/{id}', 'LogsController@destroy');
    Route::get('/truncate', 'LogsController@truncate');
});


Route::group(['prefix'=>'system-logs','middleware' => ['permission:logs.delete']],function(){
    Route::get('/', 'LogsController@systemlogs');
    Route::get('/destroy/{id}', 'LogsController@systemlogsdestroy');
    Route::get('/truncate', 'LogsController@systemlogstruncate');
});