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

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('login', 'AuthenticationController@login');
    $router->post('logout', 'AuthenticationController@logout');
    $router->post('refresh', 'AuthenticationController@refresh');
    $router->post('me', 'AuthenticationController@me');

    $router->group(['middleware' => 'checkRole:Project Manager'], function () use ($router) {
        $router->get('project-manager', 'ProjectManagerController@index');
    });
    $router->group(['middleware' => 'checkRole:Member'], function () use ($router) {
        $router->get('member', 'MemberController@index');
    });
});

$router->get('/auth/google', 'GAuthController@redirectToGoogle');
$router->get('/auth/google/callback', 'GAuthController@handleGoogleCallback');