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

Route::group(['prefix'=>'clients','middleware' => ['permission:clients.view']],function(){
    Route::get('/', 'ClientsController@index');
});

Route::group(['prefix'=>'clients','middleware' => ['permission:clients.add']],function(){
    Route::get('/create', 'ClientsController@create');
    Route::get('/verify', 'ClientsController@verify');
    Route::post('/store/', 'ClientsController@store');
});
Route::group(['prefix'=>'clients','middleware' => ['permission:clients.edit']],function(){
    Route::get('/edit/{id}', 'ClientsController@edit');
    Route::POST('/update/{id}', 'ClientsController@update');
    Route::get('/status/{id}', 'ClientsController@status');
    Route::get('/subscription/{id}', 'ClientsController@subscription');
    Route::POST('/new-subscription/{id}', 'ClientsController@newsubscription');
});
Route::group(['prefix'=>'clients','middleware' => ['permission:clients.delete']],function(){
    Route::get('/destroy/{id}', 'ClientsController@destroy');
    Route::get('/delete-subscription/{id}', 'ClientsController@deletesubscription');
});