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

Route::group(['prefix'=>'feedback-questions','middleware' => ['permission:feedback-questions.view']],function(){
    Route::get('/', 'FeedbackQuestionsController@index');
});

Route::group(['prefix'=>'feedback-questions','middleware' => ['permission:feedback-questions.add']],function(){
    Route::get('/create', 'FeedbackQuestionsController@create');
    Route::POST('/store', 'FeedbackQuestionsController@store');
    Route::get('/import', 'FeedbackQuestionsController@import');
    Route::get('/sample-export', 'FeedbackQuestionsController@exportsample');
    Route::POST('/import-store', 'FeedbackQuestionsController@importstore');

});
Route::group(['prefix'=>'feedback-questions','middleware' => ['permission:feedback-questions.edit']],function(){
    Route::get('/edit/{id}', 'FeedbackQuestionsController@edit');
    Route::POST('/update/{id}', 'FeedbackQuestionsController@update');
    Route::get('/status/{id}', 'FeedbackQuestionsController@status');
});
Route::group(['prefix'=>'feedback-questions','middleware' => ['permission:feedback-questions.delete']],function(){
    Route::get('/destroy/{id}', 'FeedbackQuestionsController@destroy');
    Route::get('/option-destroy/{id}', 'FeedbackQuestionsController@optiondestroy');
});