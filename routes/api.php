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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'  => 'v1', 'as'=>'v1.'], function(){
  Route::group(['prefix'  => 'barang', 'as'=>'barang.'], function(){
    Route::get('/data', [
      'uses'  => 'C_barang@data'
    ]);

    Route::post('/create', [
      'uses'  => 'C_barang@create'
    ]);

    Route::post('/update', [
      'uses'  => 'C_barang@update'
    ]);

    Route::post('/delete', [
      'uses'  => 'C_barang@delete'
    ]);

  });

  Route::group(['prefix'  => 'organizer', 'as'=>'organizer.'], function(){
    Route::get('/data', [
      'uses'  => 'C_organizer@data'
    ]);

    Route::post('/create', [
      'uses'  => 'C_organizer@create'
    ]);

    Route::post('/update', [
      'uses'  => 'C_organizer@update'
    ]);

    Route::post('/delete', [
      'uses'  => 'C_organizer@delete'
    ]);

  });
  
  Route::group(['prefix'  => 'event', 'as'=>'event.'], function(){
    Route::get('/data', [
      'uses'  => 'C_event@data'
    ]);

    Route::post('/create', [
      'uses'  => 'C_event@create'
    ]);

    Route::post('/update', [
      'uses'  => 'C_event@update'
    ]);

    Route::post('/delete', [
      'uses'  => 'C_event@delete'
    ]);

  });
});