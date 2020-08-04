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

Route::prefix('products')->name('products')->group(function () {
    Route::get('/', 'ProductController@index')->name('.index');
    Route::get('{product}', 'ProductController@show')->name('.show');
});

// cms crud only authorized user can
Route::middleware('auth:api')->group(function () {
    Route::prefix('products')->name('products')->group(function () {
        Route::post('/', 'ProductController@store')->name('.store');
        Route::put('{product}', 'ProductController@update')->name('.update');
        Route::delete('{product}', 'ProductController@delete')->name('.delete');
    });
});
