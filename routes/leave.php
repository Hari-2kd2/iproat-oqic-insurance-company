<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('downloadSummaryReport', 'Leave\ReportController@downloadSummaryReport');
});
Route::group(['middleware' => ['preventbackbutton', 'auth']], function () {

    // Route::group(['prefix' => 'assignEmployeeLeave'], function () {
    //     Route::get('/', ['as' => 'assignEmployeeLeave.index', 'uses' => 'Leave\AccessController@index']);
    // });

    Route::group(['prefix' => 'manageHoliday'], function () {
        Route::get('/', ['as' => 'holiday.index', 'uses' => 'Leave\HolidayController@index']);
        Route::get('/create', ['as' => 'holiday.create', 'uses' => 'Leave\HolidayController@create']);
        Route::post('/store', ['as' => 'holiday.store', 'uses' => 'Leave\HolidayController@store']);
        Route::get('/{manageHoliday}/edit', ['as' => 'holiday.edit', 'uses' => 'Leave\HolidayController@edit']);
        Route::put('/{manageHoliday}', ['as' => 'holiday.update', 'uses' => 'Leave\HolidayController@update']);
        Route::delete('/{manageHoliday}/delete', ['as' => 'holiday.delete', 'uses' => 'Leave\HolidayController@destroy']);
    });

    Route::group(['prefix' => 'publicHoliday'], function () {
        Route::get('/', ['as' => 'publicHoliday.index', 'uses' => 'Leave\PublicHolidayController@index']);
        Route::get('/create', ['as' => 'publicHoliday.create', 'uses' => 'Leave\PublicHolidayController@create']);
        Route::post('/store', ['as' => 'publicHoliday.store', 'uses' => 'Leave\PublicHolidayController@store']);
        Route::get('/{publicHoliday}/edit', ['as' => 'publicHoliday.edit', 'uses' => 'Leave\PublicHolidayController@edit']);
        Route::put('/{publicHoliday}', ['as' => 'publicHoliday.update', 'uses' => 'Leave\PublicHolidayController@update']);
        Route::delete('/{publicHoliday}/delete', ['as' => 'publicHoliday.delete', 'uses' => 'Leave\PublicHolidayController@destroy']);
    });

    Route::group(['prefix' => 'weeklyHoliday'], function () {
        Route::get('/', ['as' => 'weeklyHoliday.index', 'uses' => 'Leave\WeeklyHolidayController@index']);
        Route::get('/create', ['as' => 'weeklyHoliday.create', 'uses' => 'Leave\WeeklyHolidayController@create']);
        Route::post('/store', ['as' => 'weeklyHoliday.store', 'uses' => 'Leave\WeeklyHolidayController@store']);
        Route::get('/{weeklyHoliday}/edit', ['as' => 'weeklyHoliday.edit', 'uses' => 'Leave\WeeklyHolidayController@edit']);
        Route::put('/{weeklyHoliday}', ['as' => 'weeklyHoliday.update', 'uses' => 'Leave\WeeklyHolidayController@update']);
        Route::delete('/{weeklyHoliday}/delete', ['as' => 'weeklyHoliday.delete', 'uses' => 'Leave\WeeklyHolidayController@destroy']);
        Route::post('/import', ['as' => 'weeklyHoliday.import', 'uses' => 'Leave\WeeklyHolidayController@importWeeklyHoliday']);
    });

    Route::group(['prefix' => 'compOff'], function () {
        Route::get('/', ['as' => 'compOff.index', 'uses' => 'Leave\CompOffController@index']);
        Route::get('/create', ['as' => 'compOff.create', 'uses' => 'Leave\CompOffController@create']);
        Route::post('/store', ['as' => 'compOff.store', 'uses' => 'Leave\CompOffController@store']);
        Route::get('/{compOff}/edit', ['as' => 'compOff.edit', 'uses' => 'Leave\CompOffController@edit']);
        Route::put('/{compOff}', ['as' => 'compOff.update', 'uses' => 'Leave\CompOffController@update']);
        Route::delete('/{compOff}/delete', ['as' => 'compOff.delete', 'uses' => 'Leave\CompOffController@destroy']);
        Route::get('/getWorkingtime', ['as' => 'compOff.getWorkingtime', 'uses' => 'Leave\CompOffController@getWorkingtime']);
        Route::get('/getWorkinghours', ['as' => 'compOff.getWorkinghours', 'uses' => 'Leave\CompOffController@getWorkinghours']);
        Route::get('/getWorkingtiming', ['as' => 'compOff.getWorkingtiming', 'uses' => 'Leave\CompOffController@getWorkingtiming']);
    });
    Route::group(['prefix' => 'incentive'], function () {
        Route::get('/', ['as' => 'incentive.index', 'uses' => 'Leave\IncentiveController@index']);
        Route::get('/create', ['as' => 'incentive.create', 'uses' => 'Leave\IncentiveController@create']);
        Route::post('/store', ['as' => 'incentive.store', 'uses' => 'Leave\IncentiveController@store']);
        Route::get('/{incentive}/edit', ['as' => 'incentive.edit', 'uses' => 'Leave\IncentiveController@edit']);
        Route::put('/{incentive}', ['as' => 'incentive.update', 'uses' => 'Leave\IncentiveController@update']);
        Route::delete('/{incentive}/delete', ['as' => 'incentive.delete', 'uses' => 'Leave\IncentiveController@destroy']);
        Route::get('/getWorkingtime', ['as' => 'incentive.getWorkingtime', 'uses' => 'Leave\IncentiveController@getWorkingtime']);
    });

    Route::group(['prefix' => 'leaveType'], function () {
        Route::get('/', ['as' => 'leaveType.index', 'uses' => 'Leave\LeaveTypeController@index']);
        Route::get('/create', ['as' => 'leaveType.create', 'uses' => 'Leave\LeaveTypeController@create']);
        Route::post('/store', ['as' => 'leaveType.store', 'uses' => 'Leave\LeaveTypeController@store']);
        Route::get('/{leaveType}/edit', ['as' => 'leaveType.edit', 'uses' => 'Leave\LeaveTypeController@edit']);
        Route::put('/{leaveType}', ['as' => 'leaveType.update', 'uses' => 'Leave\LeaveTypeController@update']);
        Route::delete('/{leaveType}/delete', ['as' => 'leaveType.delete', 'uses' => 'Leave\LeaveTypeController@destroy']);
    });

    Route::group(['prefix' => 'applyForLeave'], function () {
        Route::get('/', ['as' => 'applyForLeave.index', 'uses' => 'Leave\ApplyForLeaveController@index']);
        Route::get('/create', ['as' => 'applyForLeave.create', 'uses' => 'Leave\ApplyForLeaveController@create']);
        Route::post('/store', ['as' => 'applyForLeave.store', 'uses' => 'Leave\ApplyForLeaveController@store']);
        Route::post('getEmployeeLeaveBalance', 'Leave\ApplyForLeaveController@getEmployeeLeaveBalance');
        Route::post('applyForTotalNumberOfDays', 'Leave\ApplyForLeaveController@applyForTotalNumberOfDays');
        Route::get('/{applyForLeave}', ['as' => 'applyForLeave.show', 'uses' => 'Leave\ApplyForLeaveController@show']);
    });

    Route::group(['prefix' => 'applyForOnDuty'], function () {
        Route::get('/', ['as' => 'applyForOnDuty.index', 'uses' => 'Leave\OnDutyController@index']);
        Route::get('/create', ['as' => 'applyForOnDuty.create', 'uses' => 'Leave\OnDutyController@create']);
        Route::post('/store', ['as' => 'applyForOnDuty.store', 'uses' => 'Leave\OnDutyController@store']);
        Route::get('/{applyForOnDuty}', ['as' => 'applyForOnDuty.show', 'uses' => 'Leave\OnDutyController@show']);
    });

    Route::group(['prefix' => 'applyForPermission'], function () {
        Route::get('/', ['as' => 'applyForPermission.index', 'uses' => 'Leave\ApplyForPermissionController@index']);
        Route::get('/create', ['as' => 'applyForPermission.create', 'uses' => 'Leave\ApplyForPermissionController@create']);
        Route::post('/store', ['as' => 'applyForPermission.store', 'uses' => 'Leave\ApplyForPermissionController@store']);
        Route::get('/request', ['as' => 'applyForPermission.permissionRequest', 'uses' => 'Leave\ApplyForPermissionController@permissionrequest']); 
        // Route::post('getEmployeeLeaveBalance', 'Leave\ApplyForLeaveController@getEmployeeLeaveBalance');
        Route::post('applyForTotalNumberOfPermissions', 'Leave\ApplyForPermissionController@applyForTotalNumberOfPermissions');
        // Route::get('/{applyForLeave}', ['as' => 'applyForLeave.show', 'uses' => 'ApplyForLeaveController\applyForTotalNumberOfDays@show']);
    });

    Route::group(['prefix' => 'applyForShiftChange'], function () {
        Route::get('/', ['as' => 'applyForShiftChange.index', 'uses' => 'Leave\ShiftChangeRequestController@index']);
        Route::get('/create', ['as' => 'applyForShiftChange.create', 'uses' => 'Leave\ShiftChangeRequestController@create']);
        Route::post('/store', ['as' => 'applyForShiftChange.store', 'uses' => 'Leave\ShiftChangeRequestController@store']);
        Route::get('/{applyForShiftChange}', ['as' => 'applyForShiftChange.show', 'uses' => 'Leave\ShiftChangeRequestController@show']);
    });

    Route::group(['prefix' => 'earnLeaveConfigure'], function () {
        Route::get('/', ['as' => 'earnLeaveConfigure.index', 'uses' => 'Leave\EarnLeaveConfigureController@index']);
        Route::post('updateEarnLeaveConfigure', 'Leave\EarnLeaveConfigureController@updateEarnLeaveConfigure');
    });

    Route::group(['prefix' => 'paidLeaveConfigure'], function () {
        Route::get('/', ['as' => 'paidLeaveConfigure.index', 'uses' => 'Leave\PaidLeaveConfigureController@index']);
        Route::post('updatePaidLeaveConfigure', 'Leave\PaidLeaveConfigureController@updatePaidLeaveConfigure');
    });

    Route::group(['prefix' => 'requestedApplication'], function () {
        Route::get('/', ['as' => 'requestedApplication.index', 'uses' => 'Leave\RequestedApplicationController@index']);
        Route::get('/{requestedApplication}/viewDetails', ['as' => 'requestedApplication.viewDetails', 'uses' => 'Leave\RequestedApplicationController@viewDetails']);
        Route::put('/{requestedApplication}', ['as' => 'requestedApplication.update', 'uses' => 'Leave\RequestedApplicationController@update']);
    });

    Route::group(['prefix' => 'permissionRequest'], function () {
        Route::get('/request', ['as' => 'permissionRequest.permissionRequest', 'uses' => 'Leave\ApplyForPermissionController@permissionrequest']);
    });

    Route::group(['prefix' => 'requestedOnDutyApplication'], function () {
        Route::get('/', ['as' => 'requestedOnDutyApplication.index', 'uses' => 'Leave\RequestedOnDutyApplicationController@index']);
        Route::get('/{requestedOnDutyApplication}/viewDetails', ['as' => 'requestedOnDutyApplication.viewDetails', 'uses' => 'Leave\RequestedOnDutyApplicationController@viewDetails']);
        Route::put('/{requestedOnDutyApplication}', ['as' => 'requestedOnDutyApplication.update', 'uses' => 'Leave\RequestedOnDutyApplicationController@update']);
    });

    Route::group(['prefix' => 'requestedShiftChangeRequest'], function () {
        Route::get('/', ['as' => 'requestedShiftChangeRequest.index', 'uses' => 'Leave\RequestedShiftChangeRequestController@index']);
        Route::get('/{requestedShiftChangeRequest}/viewDetails', ['as' => 'requestedShiftChangeRequest.viewDetails', 'uses' => 'Leave\RequestedShiftChangeRequestController@viewDetails']);
        Route::put('/{requestedShiftChangeRequest}', ['as' => 'requestedShiftChangeRequest.update', 'uses' => 'Leave\RequestedShiftChangeRequestController@update']);
    });

    // Route::group(['prefix' => 'requestedPaidLeaveApplication'], function () {
    //     Route::get('/', ['as' => 'requestedPaidLeaveApplication.index', 'uses' => 'Leave\RequestedPaidLeaveApplicationController@index']);
    //     Route::get('/{requestedPaidLeaveApplication}/viewDetails', ['as' => 'requestedPaidLeaveApplication.viewDetails', 'uses' => 'Leave\RequestedPaidLeaveApplicationController@viewDetails']);
    //     Route::put('/{requestedPaidLeaveApplication}', ['as' => 'requestedPaidLeaveApplication.update', 'uses' => 'Leave\RequestedPaidLeaveApplicationController@update']);
    // });

    Route::get('leaveReport', ['as' => 'leaveReport.leaveReport', 'uses' => 'Leave\ReportController@employeeLeaveReport']);
    Route::post('leaveReport', ['as' => 'leaveReport.leaveReport', 'uses' => 'Leave\ReportController@employeeLeaveReport']);
    Route::get('downloadLeaveReport', 'Leave\ReportController@downloadLeaveReport');

    Route::get('summaryReport', ['as' => 'summaryReport.summaryReport', 'uses' => 'Leave\ReportController@summaryReport']);
    Route::post('summaryReport', ['as' => 'summaryReport.summaryReport', 'uses' => 'Leave\ReportController@summaryReport']);

    Route::get('myLeaveReport', ['as' => 'myLeaveReport.myLeaveReport', 'uses' => 'Leave\ReportController@myLeaveReport']);
    Route::post('myLeaveReport', ['as' => 'myLeaveReport.myLeaveReport', 'uses' => 'Leave\ReportController@myLeaveReport']);
    Route::get('downloadMyLeaveReport', 'Leave\ReportController@downloadMyLeaveReport');

    Route::post('approveOrRejectLeaveApplication', 'Leave\RequestedApplicationController@approveOrRejectLeaveApplication');

    Route::post('approveOrRejectLeavePermissionByDepartmentHead', 'Leave\RequestedApplicationController@approveOrRejectLeavePermissionByDepartmentHead');

    Route::post('approveOrRejectOnDutyApplication', 'Leave\RequestedOnDutyApplicationController@approveOrRejectOnDutyApplication');

    Route::post('approveOrRejectShiftChangeApplication', 'Leave\RequestedShiftChangeRequestController@approveOrRejectShiftChangeApplication');
    
    Route::get('/weeklyHolidayTemplate', ['as' => 'weeklyHoliday.weeklyHolidayTemplate', 'uses' => 'Leave\WeeklyHolidayController@weeklyHolidayTemplate']);

    // Route::group(['prefix' => 'paidLeaveReport'], function () {
    Route::get('paidLeaveReport', ['as' => 'paidLeaveReport.paidLeaveReport', 'uses' => 'Leave\PaidLeaveReportController@employeePaidLeaveReport']);
    Route::post('paidLeaveReport', ['as' => 'paidLeaveReport.paidLeaveReport', 'uses' => 'Leave\PaidLeaveReportController@employeePaidLeaveReport']);
    Route::get('downloadPaidLeaveReport', 'Leave\PaidLeaveReportController@downloadPaidLeaveReport');
    Route::get('paidLeaveSummaryReport', ['as' => 'paidLeaveReport.paidLeaveSummaryReport', 'uses' => 'Leave\PaidLeaveReportController@paidLeaveSummaryReport']);
    Route::post('paidLeaveSummaryReport', ['as' => 'paidLeaveReport.paidLeaveSummaryReport', 'uses' => 'Leave\PaidLeaveReportController@paidLeaveSummaryReport']);
    Route::get('downloadPaidLeaveSummaryReport', 'Leave\PaidLeaveReportController@downloadPaidLeaveSummaryReport');
    // Route::get('myPaidLeaveReport', ['as' => 'paidLeaveReport.myPaidLeaveReport', 'uses' => 'Leave\PaidLeaveReportController@myPaidLeaveReport']);
    // Route::post('myPaidLeaveReport', ['as' => 'paidLeaveReport.myPaidLeaveReport', 'uses' => 'Leave\PaidLeaveReportController@myPaidLeaveReport']);
    // Route::get('downloadMyPaidLeaveReport', 'Leave\PaidLeaveReportController@downloadMyPaidLeaveReport');
    // });

});
