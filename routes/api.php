<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix'=>'users','middleware'=>'api'],function(){
Route::POST('verify-user', 'API\AuthController@verifyuser');
Route::POST('verify-otp', 'API\AuthController@verifyotp');
Route::POST('set-pin', 'API\AuthController@setpin');
Route::POST('login', 'API\AuthController@login');
Route::get('logout', 'API\AuthController@logout');
Route::get('refresh/{id}', 'API\AuthController@refresh');
});

Route::group(['prefix'=>'dashboard','middleware' => ['jwt.verify']],function(){
    Route::get('/', 'API\DashboardController@index');    
});