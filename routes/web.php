<?php

use App\Http\Controllers;
use App\Http\Controllers\ExampleController;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return "img.*";
});

$router->get('/getImages/{id}', 'ExampleController@getList');

$router->get('/get/{uuid}/{hash}', 'ExampleController@getImage');
