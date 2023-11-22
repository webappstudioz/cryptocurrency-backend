<?php

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
    return $router->app->version();
});

$router->group([ 'prefix'=>'v1' ], function () use ($router) {  

    /* Without authorization routes */
    $router->post('login', 'AuthController@login');  
    
    $router->group([ 'middleware' => 'auth' ], function ($router) { 

        $router->get('get/time/zone', 'TimeZoneController@getTimeZone');
        $router->post('store/time/zone', 'TimeZoneController@storeTimeZone');
    });
});
