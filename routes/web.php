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
use App\Http\Controllers\Auth\Google_auth\GAuthController_rev;
use App\Http\Controllers\Auth\JWT_Auth\AuthenticationController;
use App\Http\Controllers\Auth\Email_Verification\SendMailController;
use App\Http\Controllers\Auth\Email_Verification\UpdatePasswordController;

use App\Http\Controllers\Attendance;
use App\Http\Controllers\Attendance\History\UserAttendenceHistory;
use App\Http\Controllers\Attendance\UserRequestOptions\UserAttendanceRequest;
use App\Http\Controllers\Attendance\UserRequestOptions\UserAttendanceRequestController;
use App\Http\Controllers\Attendance\UserRequestOptions\UserShiftRequest;

use App\Http\Controllers\Dashboard\Task\ApprovedTaskController;
use App\Http\Controllers\Dashboard\Task\TaskController;
use App\Http\Controllers\Dashboard\Point\ProjectManagerController;
use App\Http\Controllers\Dashboard\Point\MemberController;
use App\Http\Controllers\Dashboard\ProjectManagement\ProjectAndTaskController;
use App\Http\Controllers\Dashboard\ProjectManagement\ProjectController;
// use App\Http\Controllers\Dashboard\ProjectManager\ProjectManagerController;

// use App\Http\Controllers\Dashboard\Member\MemberController;

use App\Http\Controllers\Dashboard\Notifications\AnnouncementController;
use App\Http\Controllers\Dashboard\Notifications\NotificationController;

use App\Http\Controllers\Dashboard\Account\FAQController;
use App\Http\Controllers\Dashboard\Account\UserEmployment;
use App\Http\Controllers\Dashboard\Account\UserProfile;

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
    /**
     * Router for Role Project Manager
     */
    $router->group(['middleware' => 'checkRole:Project Manager'], function () use ($router) {
        /**
         * Router for Project Manager
         */
        $router->get('project-manager',         'Dashboard\ProjectManager\ProjectManagerController@index');
        $router->get('/projectmanager/history', 'Attendance\History\UserAttendenceHistory@ManagerLog');

        /**
         * Router for Point
         * Router for Approved Task
         */
        $router->get('data-points',         'Dashboard\Point\ProjectManagerController@getData');
        $router->post('/add-main-points',   'Dashboard\Point\ProjectManagerController@addMainPoint');
        $router->post('/add-rewards',        'Dashboard\Point\ProjectManagerController@addRewardPointBeforeClaims');
        $router->get('task-all',            'Dashboard\Task\ApprovedTaskController@index');
        $router->put('approve-task/{id}',  'Dashboard\Task\ApprovedTaskController@edit');
        $router->get('/task-pending',       'Dashboard\Task\ApprovedTaskController@taskPending');
        $router->get('task-approved',       'Dashboard\Task\ApprovedTaskController@taskApproved');

        /**
         * Router for Project Management
         */
        $router->post('/create-project',        'Dashboard\ProjectManagement\ProjectController@createProject');
        $router->post('/edit-project/{id}',     'Dashboard\ProjectManagement\ProjectController@editProject');
        $router->post('/delete-project/{id}',   'Dashboard\ProjectManagement\ProjectController@deleteProject');
        $router->get('/project/{id}',           'Dashboard\ProjectManagement\ProjectController@detailProject');
        $router->get('/status-projects',        'Dashboard\ProjectManagement\ProjectController@statusProjects');
        $router->get('/all-projects',           'Dashboard\ProjectManagement\ProjectController@allProjects');

        /**
         * Router for Task Management
         */
        $router->post('/task/{task_id}/add-comment', 'Dashboard\Task\TaskController@addCommentToTask');
    });

    /**
     * Router for Role Member
     */
    $router->group(['middleware' => 'checkRole:Member'], function () use ($router) {
        /**
         * Router for Member
         */
        $router->get('/member',         'Dashboard\Member\MemberController@index');
        $router->get('/member/history', 'Attendance\History\UserAttendenceHistory@MemberLog');
    });

    /**
     * Router for Role Project Manager and Member
     */
    $router->group(['middleware' => 'GroupAccess:Project Manager,Member'], function () use ($router) {
        /**
         * Router for Option shift
         * Router for Option leave
         * Router for Option attendance Request
         */
        $router->post('/shift-request',     'Attendance\UserRequestOptions\UserShiftRequest@ShiftRequestProcess');
        $router->post('/leave-request',     'Attendance\UserRequestOptions\UserLeaveRequest@LeaveRequestProcess');
        $router->post('/clock-in-request',  'Attendance\UserRequestOptions\UserAttendanceRequest@ClockIn_Rev');
        $router->post('/clock-out-request', 'Attendance\UserRequestOptions\UserAttendanceRequest@ClockOut');

        /**
         * Router for Get shift schedules
         */
        $router->get('/get-shift-schedules', 'Attendance\UserRequestOptions\UserAttendanceRequest@getSchedule');

        /**
         * Router for User Profile
         */
        $router->get('/user-info',          'Dashboard\Account\UserProfile@show');
        $router->post('/update-profile',    'Dashboard\Account\UserProfile@update');
        $router->put('/change-password',    'Dashboard\Account\UserProfile@changePassword');

        /**
         * Router Informasi Pekerjaan dari status user di perusahaan
         *
         */
        $router->get('/user-employment',    'Dashboard\Account\UserEmployment@index');

        /**
         * Router data data project dan task setiap user
         *
         */
        $router->get('/get-projects-and-tasks/{user_id}', 'Dashboard\ProjectManagement\ProjectAndTaskController@getProjectsAndTasks');

        /**
         * Router for Point Member
         */
        $router->get('/points',              'Dashboard\Point\MemberController@index');
        $router->post('/claim-rewards',      'Dashboard\Point\MemberController@claimReward');
        $router->post('/transfer-points',   'Dashboard\Point\MemberController@transferPoint');

        /**
         * Router for Task Management
         */
        $router->get('/task/list-task',                 'Dashboard\Task\TaskController@getTaskList');
        $router->get('/task/{project_id}/list-task',    'Dashboard\Task\TaskController@getTasksByProject');
        $router->get('/task/{task_id}/list-comment',    'Dashboard\Task\TaskController@getCommentsForTask');
        $router->post('/task/{project_id}/add-task',    'Dashboard\Task\TaskController@addTaskToProject');
        $router->put('/task/{task_id}/edit-status',     'Dashboard\Task\TaskController@updateTaskStatus');
    });

    /**
     *
     * Pengumuman
     */
    $router->get('/list-announcements',                     'Dashboard\Notifications\AnnouncementController@index');
    $router->post('/add-announcement',                      'Dashboard\Notifications\AnnouncementController@store');
    $router->put('/update-announcement/{announcementId}',   'Dashboard\Notifications\AnnouncementController@update');
    $router->delete('/delete-announcement/{announcementId}', 'Dashboard\Notifications\AnnouncementController@destroy');


    /**
     * Notifications
     */
    $router->get('/all-notifications',              'Dashboard\Notifications\NotificationController@allNotifications');
    $router->get('/user-notifications/{user_id}',   'Dashboard\Notifications\NotificationController@userNotifications');

    /**
     * Attendance
     */
    //$router->post('/clockInOld', 'Attendance\AttenderController@ClockIn');
    $router->post('/clockIn',   'Attendance\AttenderController@ClockIn_Rev');
    $router->post('/clockOut',  'Attendance\AttenderController@ClockOut');

    /**
     * FAQ Controller
     */
    $router->get('/faq',            'Dashboard\Account\FAQController@index');
    $router->get('/faq/{id}',       'Dashboard\Account\FAQController@show');
    $router->post('/faq',           'Dashboard\Account\FAQController@store');
    $router->put('/faq/{id}',       'Dashboard\Account\FAQController@update');
    $router->delete('/faq/{id}',    'Dashboard\Account\FAQController@destroy');
});


/**
 * Google Auth
 */
$router->get('/auth/google/login',      'Auth\Google_auth\GAuthController_rev@redirectToGoogle');
$router->get('/auth/google/callback',   'Auth\Google_auth\GAuthController_rev@handleGoogleCallback');
