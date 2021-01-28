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

Route::group(['prefix' => 'v1', 'middleware' => 'jwt.verify'], function () {
    Route::post('logout', 'UserController@logout')->name('users.logout');
    Route::put('update', 'UserController@update')->name('users.update');
    Route::post('destroy', 'UserController@destroy')->name('users.destroy');
    Route::post('getuser', 'UserController@getUser')->name('users.getuser');
});

Route::post('reset', 'ForgotPasswordController@getResetToken')->name('reset');
Route::post('renew', 'ResetPasswordController@renew')->name('renew');
Route::post('register', 'UserController@store')->name('users.store');
Route::post('search', 'UserController@searchByNameAndCareer')->name('users.search');
Route::post('login', 'UserController@login')->name('users.login');
