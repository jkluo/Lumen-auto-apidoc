<?php

use Illuminate\Support\Facades\Route;

Route::get('doc', 'DocController@index');
Route::get('doc/search', 'DocController@search');
Route::get('doc/list', "DocController@getList");
Route::get('doc/info', "DocController@getInfo");
Route::get('doc/debug', "DocController@debug");
Route::post('doc/debug', "DocController@debug");