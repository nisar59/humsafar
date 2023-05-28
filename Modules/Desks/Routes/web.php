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

Route::group(['prefix'=>'desks','middleware' => ['permission:desks.view']],function(){
    Route::get('/', 'DesksController@index');
});

Route::group(['prefix'=>'desks','middleware' => ['permission:desks.add']],function(){
    Route::get('/create', 'DesksController@create');
    Route::get('/create/{id}', 'DesksController@store');
});
Route::group(['prefix'=>'desks','middleware' => ['permission:desks.edit']],function(){
    Route::get('/edit/{id}', 'DesksController@edit');
    Route::POST('/update/{id}', 'DesksController@update');
    Route::get('/status/{id}', 'DesksController@status');
});
Route::group(['prefix'=>'desks','middleware' => ['permission:desks.delete']],function(){
    Route::get('/destroy/{id}', 'DesksController@destroy');
});