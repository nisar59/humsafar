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

Route::group(['prefix'=>'settings','middleware' => ['permission:settings.view']],function(){
    Route::get('/', 'SettingsController@index');
});

Route::group(['prefix'=>'settings','middleware' => ['permission:settings.add']],function(){
    Route::POST('/store', 'SettingsController@store');

});

Route::group(['prefix'=>'settings','middleware' => ['permission:settings.add']],function(){
    Route::POST('/theme', 'SettingsController@theme');

});

Route::group(['prefix'=>'settings','middleware' => ['permission:settings.add']],function(){
    Route::POST('/restorydefault', 'SettingsController@restorydefault');

});