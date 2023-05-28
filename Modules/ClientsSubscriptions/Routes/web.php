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

Route::group(['prefix'=>'clients-subscriptions','middleware' => ['permission:clients-subscriptions.view']],function(){
    Route::get('/', 'ClientsSubscriptionsController@index');
    Route::get('/export', 'ClientsSubscriptionsController@export');
});

Route::group(['prefix'=>'clients-subscriptions','middleware' => ['permission:clients-subscriptions.add']],function(){
    Route::get('/create', 'ClientsSubscriptionsController@create');
    Route::post('/store/', 'ClientsSubscriptionsController@store');
});
Route::group(['prefix'=>'clients-subscriptions','middleware' => ['permission:clients-subscriptions.edit']],function(){
    Route::get('/edit/{id}', 'ClientsSubscriptionsController@edit');
    Route::POST('/update/{id}', 'ClientsSubscriptionsController@update');
    Route::get('/status/{id}', 'ClientsSubscriptionsController@status');
    Route::get('/services/{id}', 'ClientsSubscriptionsController@services');
    Route::post('/bulk-services/', 'ClientsSubscriptionsController@bulkservices');
    Route::get('/subscription/{id}', 'ClientsSubscriptionsController@subscription');
    Route::POST('/new-subscription/{id}', 'ClientsSubscriptionsController@newsubscription');
});
Route::group(['prefix'=>'clients-subscriptions','middleware' => ['permission:clients-subscriptions.delete']],function(){
    Route::get('/destroy/{id}', 'ClientsSubscriptionsController@destroy');
    Route::get('/delete-subscription/{id}', 'ClientsSubscriptionsController@deletesubscription');
});