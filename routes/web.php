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

Route::get('/', function () {
    return view('pages/dashboard/index');
});

Route::group(['prefix'  => 'barang', 'as'=>'barang.'], function(){
	Route::get('/', array('uses' => 'C_barang@index', 'as' => 'index'));
    Route::get('/form', array('uses' => 'C_barang@get_form'));
    Route::get('/form/{id}', array('uses' => 'C_barang@get_form'));
});

Route::group(['prefix'  => 'organizer', 'as'=>'organizer.'], function(){
	Route::get('/', array('uses' => 'C_organizer@index', 'as' => 'index'));
    Route::get('/form', array('uses' => 'C_organizer@get_form'));
    Route::get('/form/{id}', array('uses' => 'C_organizer@get_form'));
});

Route::group(['prefix'  => 'event', 'as'=>'event.'], function(){
	Route::get('/', array('uses' => 'C_event@index', 'as' => 'index'));
    Route::get('/form', array('uses' => 'C_event@get_form'));
    Route::get('/form/{id}', array('uses' => 'C_event@get_form'));
});
