<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('login');
});

Route::get('register', function () {
    return redirect('login');
});

Auth::routes(['register' => false]);
Route::any('logout', 'Auth\LoginController@logout');

Route::get('check-auth', 'HomeController@checkauth');
Route::get('lock-screen', 'HomeController@lockscreen');

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');
Route::get('/artisan/{command}', 'HomeController@artisan')->name('home')->middleware('auth');
Route::get('/export-sample/', 'HomeController@sample')->middleware('auth');
