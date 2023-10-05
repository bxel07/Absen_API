<<<<<<< HEAD
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

use App\Http\Controllers\Auth\Google_auth\GAuthController;
use App\Http\Controllers\Auth\JWT_Auth\AuthenticationController;
use App\Http\Controllers\Attendance;
use App\Http\Controllers\Attendance\History\UserAttendenceHistory;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * JWT AUTH ROUTER
 */
$router->group(['prefix' => 'api', 'middleware' => 'cors'], function () use ($router) {
    $router->post('/login', 'Auth\JWT_Auth\AuthenticationController@login');
    $router->post('/logout', 'Auth\JWT_Auth\AuthenticationController@logout');
    $router->post('/refresh', 'Auth\JWT_Auth\AuthenticationController@refresh');
    $router->post('/me', 'Auth\JWT_Auth\AuthenticationController@me');

    $router->group(['middleware' => 'checkRole:Project Manager'], function () use ($router) {
        $router->get('project-manager', 'ProjectManagerController@index');
        $router->get('/projectmanager/history', 'Attendance\History\UserAttendenceHistory@ManagerLog');
    });
    $router->group(['middleware' => 'checkRole:Member'], function () use ($router) {
        $router->get('/member', 'MemberController@index');
        $router->get('/member/history', 'Attendance\History\UserAttendenceHistory@MemberLog');
    });


    /**
     * Attendance Router
     */
    //$router->post('/clockInOld', 'Attendance\AttenderController@ClockIn');

    $router->post('/clockIn', 'Attendance\AttenderController@ClockIn_Rev');
    $router->post('/clockOut', 'Attendance\AttenderController@ClockOut');

    /**
     * User Login History
     */
});

 /**
     * Google Auth Router
     */

     $router->get('/auth/google/login', 'Auth\Google_auth\GAuthController@redirectToGoogle');
     $router->get('/auth/google/callback', 'Auth\Google_auth\GAuthController@handleGoogleCallback');
