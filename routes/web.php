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
    $router->get('timezone/list', 'TimeZoneController@getTimeZone');
    $router->get('get/game/results', 'TimeZoneController@getGameResults');

    $router->group([ 'prefix' => 'get'], function ($router) { 
        $router->get('countries', 'CommonController@countries');
        $router->post('states', 'CommonController@stateByCountry');
    });

    $router->group([ 'middleware' => 'auth' ], function ($router) { 
        $router->post('store/results', 'TimeZoneController@storeTimeZone');
        $router->post('update/profile','AuthController@detailUpdate');
        $router->get('get/profile','AuthController@profile');
        $router->group([ 'prefix' => 'user' ], function ($router) { 
            $router->get('list', 'UserController@getList');
            $router->get('detail/{id}', 'UserController@detail');
            $router->post('detail/{id}', 'UserController@detail');
            $router->post('change/status', 'UserController@changeStatus');
        });
    });
    
    $router->group([ 'prefix' => 'user' ], function ($router) { 
        $router->post('register', 'AuthController@register');
        $router->get('otp/resend/{token}', 'AuthController@otpResend');
        $router->post('otp/verify', 'AuthController@otpVerify');
       
    });
});
