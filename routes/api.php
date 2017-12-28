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

Route::group(['namespace' => 'Api', 'middleware' => ['api'], 'prefix' => 'v3'], function () use ($router) {

    $router->get('/test/ceshi','TestController@test');
    //CIB
    $router->get('/cib/interest', 'CibController@interest');
    $router->get('/cib/balance', 'CibController@balance');
    $router->post('/cib/callback', 'CibController@callback');



});
