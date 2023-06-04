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

Route::group(['prefix'=>'deposits','middleware' => ['permission:deposits.view']],function(){
    Route::get('/', 'DepositsController@index');
    Route::get('/show/{id}', 'DepositsController@show');
    Route::get('/export/', 'DepositsController@export');

});

Route::group(['prefix'=>'deposits','middleware' => ['permission:deposits.add']],function(){
    Route::get('/create', 'DepositsController@create');
    Route::get('/create/{id}', 'DepositsController@store');
});
Route::group(['prefix'=>'deposits','middleware' => ['permission:deposits.edit']],function(){
    Route::get('/edit/{id}', 'DepositsController@edit');
    Route::POST('/update/{id}', 'DepositsController@update');
    Route::get('/status/{id}', 'DepositsController@status');
    Route::get('/verify/{id}', 'DepositsController@verify');
    Route::post('/bulk-verification/', 'DepositsController@bulkverification');
    Route::get('/compensation/{id}', 'DepositsController@compensation');
    Route::post('/compensation-store/{id}', 'DepositsController@compensationstore');
});
Route::group(['prefix'=>'deposits','middleware' => ['permission:deposits.delete']],function(){
    Route::get('/destroy/{id}', 'DepositsController@destroy');
});