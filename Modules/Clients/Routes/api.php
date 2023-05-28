<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix'=>'clients','middleware' => ['jwt.verify']],function(){
    Route::get('/', 'API\ClientsController@index');    
    Route::get('/create/', 'API\ClientsController@create');    
    Route::post('/store/', 'API\ClientsController@store');    
    Route::get('/find/{id}', 'API\ClientsController@edit');
    Route::POST('/update/{id}', 'API\ClientsController@update');
    Route::get('/status/{id}', 'API\ClientsController@status');    
    Route::get('/destroy/{id}', 'API\ClientsController@destroy');
    Route::POST('/new-subscription/{id}', 'API\ClientsController@newsubscription');
    Route::POST('/search/', 'API\ClientsController@show');

});

