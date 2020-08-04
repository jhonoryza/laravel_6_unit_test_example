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

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('products')->name('products')->group(function(){
    Route::get('/', 'ProductController@index')->name('.index');
    Route::post('/', 'ProductController@store')->name('.store');
    Route::put('{product}', 'ProductController@update')->name('.update');
    Route::delete('{product}', 'ProductController@delete')->name('.delete');
});
