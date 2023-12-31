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
use App\Http\Controllers\Auth\Email_Verification\SendMailController;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * JWT AUTH ROUTER
 */
$router->group(['prefix' => 'api', 'middleware' => 'cors'], function () use ($router) {
    /**
     * Login JWT
     */
    $router->post('/login', 'Auth\JWT_Auth\AuthenticationController@login');
    $router->post('/logout', 'Auth\JWT_Auth\AuthenticationController@logout');
    $router->post('/refresh', 'Auth\JWT_Auth\AuthenticationController@refresh');
    $router->post('/me', 'Auth\JWT_Auth\AuthenticationController@me');

    /**
     * Router reset password
     */

    $router->get('/forgot-password', 'Auth\Email_Verification\SendMailController@pageForgotPassword');
    $router->post('/send-mail', 'Auth\Email_Verification\SendMailController@sendMailVerification');
    $router->post('/verify-otp', 'Auth\Email_Verification\SendMailController@verifyOtp');
    $router->post('/update-password', 'Auth\Email_Verification\UpdatePasswordController@updatePassword');
    $router->get('/reset-password', 'Auth\Email_Verification\SendMailController@pageResetPassword');

    /**
     * Redirect to Dashboard Interface
     */
    $router->group(['middleware' => 'checkRole:Project Manager'], function () use ($router) {
        $router->get('project-manager', 'ProjectManagerController@index');
        $router->get('/projectmanager/history', 'Attendance\History\UserAttendenceHistory@ManagerLog');
    });
    $router->group(['middleware' => 'checkRole:Member'], function () use ($router) {
        $router->get('/member', 'MemberController@index');
        $router->get('/member/history', 'Attendance\History\UserAttendenceHistory@MemberLog');
    });

    $router->group(['middleware' => 'GroupAccess:Project Manager,Member'], function () use ($router) {
        /**
         * Router for Option shift, leave, attendance Request
         */
        $router->post('/shift-request', 'Attendance\UserRequestOptions\UserShiftRequest@ShiftRequestProcess');
        $router->post('/leave-request', 'Attendance\UserRequestOptions\UserLeaveRequest@LeaveRequestProcess');
        $router->post('/clock-in-request', 'Attendance\UserRequestOptions\UserAttendanceRequest@ClockIn_Rev');
        $router->post('/clock-out-request', 'Attendance\UserRequestOptions\UserAttendanceRequest@ClockOut');
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

     $router->get('/auth/google/login', 'Auth\Google_auth\GAuthController_rev@redirectToGoogle');
     $router->get('/auth/google/callback', 'Auth\Google_auth\GAuthController_rev@handleGoogleCallback');
