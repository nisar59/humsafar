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

Route::group(['prefix'=>'feedback','middleware' => ['permission:feedback.view']],function(){
    Route::get('/', 'FeedbackController@index');
    Route::get('/show/{id}', 'FeedbackController@show');
});

Route::group(['prefix'=>'feedback','middleware' => ['permission:feedback.add']],function(){
    Route::get('/create', 'FeedbackController@create');
    Route::post('/store/', 'FeedbackController@store');
});
Route::group(['prefix'=>'feedback','middleware' => ['permission:feedback.edit']],function(){
    Route::get('/edit/{id}', 'FeedbackController@edit');
    Route::POST('/update/{id}', 'FeedbackController@update');
});
Route::group(['prefix'=>'feedback','middleware' => ['permission:feedback.delete']],function(){
    Route::get('/destroy/{id}', 'FeedbackController@destroy');
});