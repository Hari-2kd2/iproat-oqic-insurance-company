<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menus')->truncate();
        DB::insert("INSERT INTO `menus` (`id`, `parent_id`, `action`, `name`, `menu_url`, `module_id`, `status`) VALUES
        (1,0,	NULL,	'User',	'user.index',	1,	2),
        (2,0,	NULL,	'Manage Role',	NULL,	1,	1),
        (3,2,	NULL,	'Add Role',	'userRole.index',	1,	1),
        (4,2,	NULL,	'Add Role Permission',	'rolePermission.index',	1,	1),
        (5,0,	NULL,	'Change Password',	'changePassword.index',	1,	1),
        (6,0,	NULL,	'Department',	'department.index',	2,	1),
        (7,0,	NULL,	'Designation',	'designation.index',	2,	1),
        (8,0,	NULL,	'Branch',	'branch.index',	2,	1),
        (9,0,	NULL,	'Manage Employee',	'employee.index',	2,	1),
        (10,0,	NULL,	'Setup',	NULL,	3,	1),
        (11,10,	NULL,	'Manage Holiday',	'holiday.index',	3,	1),
        (12,10,	NULL,	'Public Holiday',	'publicHoliday.index',	3,	1),
        (13,10,	NULL,	'Weekly Holiday',	'weeklyHoliday.index',	3,	1),
        (14,10,	NULL,	'Leave Type',	'leaveType.index',	3,	1),
        (15,0,	NULL,	'Leave Application',	NULL,	3,	1),
        (16,15,	NULL,	'Apply for Leave',	'applyForLeave.index',	3,	1),
        (17,15,	NULL,	'Leave Application',	'requestedApplication.index',	3,	1),
        (18,0,	NULL,	'Setup',	NULL,	4,	1),
        (19,18,	NULL,	'Manage Work Shift',	'workShift.index',	4,	1),
        (20,0,	NULL,	'Report',	NULL,	4,	1),
        (21,20,	NULL,	'Daily Attendance',	'dailyAttendance.dailyAttendance',	4,	1),
        (22,0,	NULL,	'Report',	NULL,	3,	1),
        (23,22,	NULL,	'Leave Report',	'leaveReport.leaveReport',	3,	1),
        (24,20,	NULL,	'Monthly Attendance',	'monthlyAttendance.monthlyAttendance',	4,	1),
        (30,0,	NULL,	'Setup',	NULL,	6,	0),
        (31,30,	NULL,	'Tax Rule Setup',	'taxSetup.index',	6,	0),
        (32,0,	NULL,	'Allowance',	'allowance.index',	6,	0),
        (33,0,	NULL,	'Deduction',	'deduction.index',	6,	0),
        (34,0,	NULL,	'Advance Deduction',	'advanceDeduction.index',	6,	0),
        (35,0,	NULL,	'Paid Leave Application',	NULL,	3,	0),
        (36,0,	NULL,	'Monthly Pay Grade',	'payGrade.index',	6,	0),
        (37,0,	NULL,	'Hourly Pay Grade',	'hourlyWages.index',	6,	0),
        (38,0,	NULL,	'Salary Sheet',	NULL,	6,	0),
        (39,30,	NULL,	'Late Configration',	'salaryDeductionRule.index',	6,	0),
        (40,0,	NULL,	'Report',	NULL,	6,	0),
        (41,40,	NULL,	'Payment History',	'paymentHistory.paymentHistory',	6,	0),
        (42,40,	NULL,	'My Payroll',	'myPayroll.myPayroll',	6,	0),
        (43,0,	NULL,	'Performance Category',	'performanceCategory.index',	7,	0),
        (44,0,	NULL,	'Performance Criteria',	'performanceCriteria.index',	7,	0),
        (45,0,	NULL,	'Employee Performance',	'employeePerformance.index',	7,	0),
        (46,0,	NULL,	'Report',	NULL,	7,	0),
        (47,46,	NULL,	'Summary Report',	'performanceSummaryReport.performanceSummaryReport',	7,	0),
        (48,0,	NULL,	'Job Post',	'jobPost.index',	8,	0),
        (49,0,	NULL,	'Job Candidate',	'jobCandidate.index',	8,	0),
        (50,20,	NULL,	'My Attendance Report',	'myAttendanceReport.myAttendanceReport',	4,	1),
        (51,10,	NULL,	'Earn Leave Configure',	'earnLeaveConfigure.index',	3,	0),
        (52,0,	NULL,	'Training Type',	'trainingType.index',	9,	0),
        (53,0,	NULL,	'Training List',	'trainingInfo.index',	9,	0),
        (54,0,	NULL,	'Training Report',	'employeeTrainingReport.employeeTrainingReport',	9,	0),
        (55,0,	NULL,	'Award',	'award.index',	10,	0),
        (56,0,	NULL,	'Notice',	'notice.index',	11,	0),
        (57,0,	NULL,	'Settings',	'generalSettings.index',	12,	0),
        (58,0,	NULL,	'Manual Attendance',	'manualAttendance.manualAttendance',	4,	1),
        (59,22,	NULL,	'Summary Report',	'summaryReport.summaryReport',	3,	1),
        (60,22,	NULL,	'My Leave Report',	'myLeaveReport.myLeaveReport',	3,	1),
        (61,0,	NULL,	'Warning',	'warning.index',	2,	0),
        (62,0,	NULL,	'Termination',	'termination.index',	2,	0),
        (63,0,	NULL,	'Promotion',	'promotion.index',	2,	0),
        (64,20,	NULL,	'Summary Report',	'attendanceSummaryReport.attendanceSummaryReport',	4,	1),
        (65,0,	NULL,	'Manage Work Hour',	NULL,	6,	0),
        (66,65,	NULL,	'Approve Work Hour',	'workHourApproval.create',	6,	0),
        (67,0,	NULL,	'Employee Permanent',	'permanent.index',	2,	0),
        (68,0,	NULL,	'Manage Bonus',	NULL,	6,	0),
        (69,68,	NULL,	'Bonus Setting',	'bonusSetting.index',	6,	0),
        (70,68,	NULL,	'Generate Bonus',	'generateBonus.index',	6,	0),
        (71,18,	NULL,	'Dashboard Attendance',	'attendance.dashboard',	4,	0),
        (72,0,	NULL,	'Front Setting',	NULL,	12,	0),
        (73,72,	NULL,	'General Setting',	'front.setting',	12,	0),
        (74,72,	NULL,	'Front Service',	'service.index',	12,	0),
        (75,38,	NULL,	'Generate Salary Sheet',	'generateSalarySheet.index',	6,	0),
        (76,38,	NULL,	'Download Payslip',	'downloadPayslip.payslip',	6,	0),
        (77,68,	NULL,	'Bonus Day',	'bonusday.index',	6,	0),
        (78,0,	NULL,	'Upload Attendance',	'uploadAttendance.uploadAttendance',	4,	0),
        (79,38,	NULL,	'Upload Salary Details',	'uploadSalaryDetails.uploadSalaryDetails',	6,	0),
        (80,0,	NULL,	'Paid Leave Report',	NULL,	3,	0),
        (81,80,	NULL,	'Leave Report',	'paidLeaveReport.paidLeaveReport',	3,	0),
        (82,80,	NULL,	'Summary Report',	'paidLeaveReport.paidLeaveSummaryReport',	3,	0),
        (83,10,	NULL,	'Paid Leave Configure',	'paidLeaveConfigure.index',	3,	0),
        (84,30,	NULL,	'Food Deductions Configure',	'foodDeductionConfigure.index',	6,	0),
        (85,30,	NULL,	'Telephone Deductions Configure',	'telephoneDeductionConfigure.index',	6,	0),
        (86,0,	NULL,	'Monthly Deductions',	'monthlyDeduction.monthlyDeduction',	6,	0),
        (87,18,	NULL,	'Configure Devices',	'deviceConfigure.index',	4,	0),
        (88,0,	NULL,	'Employee Access',	'access.index',	4,	0),
        (90,0,	NULL,	'Mobile Attendance',	'mobileAttendance.mobileAttendance',	4,	1),
        (91,20,	NULL,	'Attendance Records',	'attendanceRecord.attendanceRecord',	4,	1),
        (92,20,	NULL,	'Generate Report',	'calculateAttendance.calculateAttendance',	4,	1),
        (94,18,	NULL,	'Shift Details',	'shiftDetails.index',	4,	1),
        (95,18,	NULL,	'Overtime Approval',	'overtimeApproval.overtimeApproval',	4,	1),
        (96,0,	NULL,	'Comp Off',	'compOff.index',	3,	1),
        (97,0,	NULL,	'2FA',	'2fa',	1,	1),
        (98,10,	NULL,	'Incentive',	'incentive.index',	  3,	0),
        (99,0,	NULL,	'Shift Change Request',	NULL,	  3,	1),
        (100,99,NULL,	'Apply for Shift Change',	'applyForShiftChange.index',	  3,	1),
        (101,99,NULL,	'Shift Request',	'requestedShiftChangeRequest.index',	  3,	1),
        (102,0,	NULL,	'On Duty Application',	NULL,	  3,	1),
        (103,102,NULL,	'Apply for On Duty',	'applyForOnDuty.index',	  3,	1),
        (104,102,NULL,	'OnDuty Request',	'requestedOnDutyApplication.index',	  3,	1),
        (105,0,	NULL,	'Permission Application',	NULL,	  3,	1),
        (106,105,NULL,	'Apply for Permission',	'applyForPermission.index',	  3,	1),
        (107,105,NULL,	'Permission Request',	'permissionRequest.permissionRequest',	  3,	1)");
    }
}
