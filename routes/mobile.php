<?php

use Illuminate\Support\Facades\Route;

Route::get('claim/index', 'Api\ClaimController@index');
Route::post('claim/store', 'Api\ClaimController@store');

Route::group(['middleware' => ['api'], 'prefix' => 'mobile'], function () {
    Route::post('login', 'Api\AuthController@login');
    Route::post('register', 'Api\AuthController@register');
    Route::post('logout', 'Api\AuthController@logout');
    Route::get('refresh', 'Api\AuthController@refresh');

    Route::group(['prefix' => 'attendance'], function () {
        Route::post('employee_attendance_list', 'Api\AttendanceController@apiattendancelist');
        Route::get('my_attendance_report', 'Api\AttendanceReportController@myAttendanceReport');
        Route::get('download_my_attendance', 'Api\AttendanceReportController@downloadMyAttendance');
    });

    Route::group(['prefix' => 'leave'], function () {
        Route::get('index', 'Api\ApplyForLeaveController@index');
        Route::get('create', 'Api\ApplyForLeaveController@create');
        Route::post('store', 'Api\ApplyForLeaveController@store');
        Route::post('update', 'Api\ApplyForLeaveController@update');
    });

    Route::post('change_password', 'Api\AuthController@changePassword');
    Route::post('forgetpassword', 'Api\AuthController@forgetPassword');

    Route::group(['prefix' => 'permission'], function () {
        // Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'permission'], function () {
        Route::get('index', 'Api\ApplyForPermissionController@index');
        Route::get('create', 'Api\ApplyForPermissionController@create');
        Route::post('store', 'Api\ApplyForPermissionController@store');
    });

    Route::group(['prefix' => 'onduty'], function () {
        // Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'permission'], function () {
        Route::get('index', 'Api\OnDutyController@index');
        Route::get('create', 'Api\OnDutyController@create');
        Route::post('store', 'Api\OnDutyController@store');
    });

    Route::group(['prefix' => 'shiftchange'], function () {
        // Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'permission'], function () {
        Route::get('index', 'Api\ShiftChangeRequestController@index');
        Route::get('create', 'Api\ShiftChangeRequestController@create');
        Route::post('store', 'Api\ShiftChangeRequestController@store');
    });
    Route::group(['prefix' => 'compOff'], function () {
        // Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'permission'], function () {
        Route::get('index', 'Api\CompOffController@index');
        Route::get('create', 'Api\CompOffController@create');
        Route::post('store', 'Api\CompOffController@store');
        Route::get('getWorkingtime', 'Api\CompOffController@getWorkingtime');
        
    }); 
});
