<?php

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
Route::post('login', 'LoginController@login')->name('login');

Route::prefix('products')->name('products')->group(function () {
    Route::get('/', 'ProductController@index')->name('.index');
    Route::get('{product}', 'ProductController@show')->name('.show');
});

Route::prefix('categories')->name('categories')->group(function () {
    Route::get('/', 'CategoryController@index')->name('.index');
    Route::get('{category}', 'CategoryController@show')->name('.show');
});

// cms crud only authorized user can
Route::middleware('auth:api')->group(function () {
    Route::prefix('products')->name('products')->group(function () {
        Route::post('/', 'ProductController@store')->name('.store');
        Route::put('{product}', 'ProductController@update')->name('.update');
        Route::delete('{product}', 'ProductController@delete')->name('.delete');
    });

    Route::prefix('categories')->name('categories')->group(function () {
        Route::post('/', 'CategoryController@store')->name('.store');
        Route::put('{category}', 'CategoryController@update')->name('.update');
        Route::delete('{category}', 'CategoryController@delete')->name('.delete');
    });
});
