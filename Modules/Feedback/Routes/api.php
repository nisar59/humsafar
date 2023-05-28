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

Route::group(['prefix'=>'feedback','middleware' => ['jwt.verify']],function(){
    Route::get('/', 'API\FeedbackController@index');    
    Route::get('/create/{type}', 'API\FeedbackController@create');    
    Route::post('/store/', 'API\FeedbackController@store');    
    Route::get('/find/{id}', 'API\FeedbackController@edit');
    Route::POST('/update/{id}', 'API\FeedbackController@update');
    Route::get('/status/{id}', 'API\FeedbackController@status');    
    Route::get('/destroy/{id}', 'API\FeedbackController@destroy');
    Route::POST('/new-subscription/{id}', 'API\FeedbackController@newsubscription');
    Route::POST('/search/', 'API\FeedbackController@show');

});