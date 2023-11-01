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
use App\Http\Controllers\Task\TaskController;
use OpenApi\Annotations\OpenApi as OA;

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
        $router->get('project-manager', 'Dashboard\ProjectManager\ProjectManagerController@index');
        $router->get('/projectmanager/history', 'Attendance\History\UserAttendenceHistory@ManagerLog');

        // Router Informasi Point Project Manager
        $router->get('data-points', 'Dashboard\Point\ProjectManagerController@getData');
        $router->post('/add-main-points', 'Dashboard\Point\ProjectManagerController@addMainPoint');
        $router->post('/add-reward', 'Dashboard\Point\ProjectManagerController@addRewardPointBeforeClaims');

        $router->get('task-all', 'Task\ApprovedTaskController@index');
        $router->post('approve-task', 'Task\ApprovedTaskController@edit');
    });
    $router->group(['middleware' => 'checkRole:Member'], function () use ($router) {
        $router->get('/member', 'Dashboard\Member\MemberController@index');
        $router->get('/member/history', 'Attendance\History\UserAttendenceHistory@MemberLog');

        // Router Informasi Point Member
        $router->get('/point', 'Dashboard\Point\MemberController@index');
        $router->post('/claim-reward', 'Dashboard\Point\MemberController@claimReward');
        $router->post('/transfer-points', 'Dashboard\Point\MemberController@transferPoint');
    });

    $router->group(['middleware' => 'GroupAccess:Project Manager,Member'], function () use ($router) {
        /**
         * Router for Option shift, leave, attendance Request
         */
        $router->post('/shift-request', 'Attendance\UserRequestOptions\UserShiftRequest@ShiftRequestProcess');
        $router->post('/leave-request', 'Attendance\UserRequestOptions\UserLeaveRequest@LeaveRequestProcess');
        $router->post('/clock-in-request', 'Attendance\UserRequestOptions\UserAttendanceRequest@ClockIn_Rev');
        $router->post('/clock-out-request', 'Attendance\UserRequestOptions\UserAttendanceRequest@ClockOut');

        /**
         * Get shift schedules
         */
        $router->get('/get-shift-schedules', 'Attendance\UserRequestOptions\UserAttendanceRequest@getSchedule');

        /**
         * Router Informasi Akun
         */
        $router->get('/user-info', 'Dashboard\Profile\UserProfile@show');
        $router->post('/update-profile', 'Dashboard\Profile\UserProfile@update');

        /**
         * Router Informasi Pekerjaan dari status user di perusahaan
         *
         */
        $router->get('/user-employment', 'Dashboard\Profile\UserEmployment@index');

        /**
         * Router data data project dan task setiap user
         *
         */
        $router->get('/get-projects-and-tasks/{user_id}', 'Dashboard\Project\ProjectController@getProjectsAndTasks');
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

    /**
     * Project Router
     */
    $router->group(['middleware' => 'GroupAccess:Project Manager,Member'], function () use ($router) {
        // Semua List Task
        $router->get('/project-manager/list-task', 'Task\TaskController@getTaskList');
        // List Task by Project
        $router->get('/project-manager/{project_id}/list-task', 'Task\TaskController@getTasksByProject');
        // List Comment by Task
        $router->get('/project-manager/{task_id}/list-comment', 'Task\TaskController@getCommentsForTask');
        // Add Task by Project
        $router->post('/project-manager/{project_id}/add-task', 'Task\TaskController@addTaskToProject');
    });
    $router->group(['middleware' => 'checkRole:Project Manager'], function () use ($router) {
        $router->post('/project-manager/{task_id}/add-comment', 'Task\TaskController@addCommentToTask');
    });
});

/**
 * Google Auth Router
 */

$router->get('/auth/google/login', 'Auth\Google_auth\GAuthController_rev@redirectToGoogle');
$router->get('/auth/google/callback', 'Auth\Google_auth\GAuthController_rev@handleGoogleCallback');

/**
 * API DOCS
 */
