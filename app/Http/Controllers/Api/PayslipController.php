<?php

namespace App\Http\Controllers\Api;

use App\Model\SalaryDetails;
use Illuminate\Http\Request;
use App\Model\PrintHeadSetting;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use App\Model\SalaryDetailsToLeave;
use App\Http\Controllers\Controller;
use App\Repositories\CommonRepository;
use App\Model\SalaryDetailsToAllowance;
use App\Model\SalaryDetailsToDeduction;

class PayslipController extends Controller
{
    protected $commonRepository;
    protected $controller;

    public function __construct(Controller $controller, CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
        $this->controller  = $controller;
    }


    public function myPayroll(Request $request)
    {

        $employee_id = $request->employee_id;

        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with('payGrade');
        }])->where('status', 1)->where('employee_id', $employee_id)->orderBy('salary_details_id', 'DESC')->get();

        return response()->json([
            'message' => "My paroll details received successfully",
            'data' =>  $results,
        ], 200);
    }

    public function payslip(Request $request)
    {

        $employee_id = $request->employee_id;

        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'payGrade', 'hourlySalaries']);
        }])->where('employee_id', $employee_id)->orderBy('salary_details_id', 'DESC')->get();

        if ($request->month_of_salary) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'payGrade', 'hourlySalaries']);
            }])->where('employee_id', $request->employee_id)->orderBy('salary_details_id', 'DESC');

            if ($request->month_of_salary != '') {
                $results->where('status', 1)->where('month_of_salary', $request->month_of_salary);
            }

            $results = $results->get();

            if ($results != []) {
                return response()->json([
                    'message' => "My payslip details received successfully.",
                    'data' =>  $results,
                ], 200);
            }else{
                return response()->json([
                    'message' => "No records found.",
                    'data' =>  $results,
                ], 200);
            }
        }

        $departmentList = $this->commonRepository->departmentList();

        if ($results != [] && $departmentList != []) {
            return response()->json([
                'message' => "My payslip details received successfully.",
                'departmentList' =>  $departmentList,
                'data' =>  $results,
            ], 200);
        }else{
            return response()->json([
                'message' => "No records found.",
                'departmentList' =>  $departmentList,
                'data' =>  $results,
            ], 200);
        }

        
    }

    public function downloadMyPayroll(Request $request)
    {

        $employee_id = $request->employee_id;
        $printHeadSetting = PrintHeadSetting::first();

        $results          = SalaryDetails::with(['employee' => function ($query) {
            $query->with('payGrade');
        }])->where('status', 1)->where('employee_id', $employee_id)->orderBy('salary_details_id', 'DESC')->get();

        $data = [
            'results'   => $results,
            'printHead' => $printHeadSetting,
        ];

        $pdf = PDF::loadView('admin.payroll.report.pdf.myPayrollPdf', $data);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("my-payroll-Pdf.pdf");
    }

    public function downloadPayslip(Request $request)
    {
        $employee_id = $request->employee_id;
        $month_of_salary = $request->month_of_salary;
        $ifHourly  = SalaryDetails::with(['employee' => function ($q) {
            $q->with(['hourlySalaries', 'department', 'designation']);
        }])->where('month_of_salary', $month_of_salary)->where('employee_id', $employee_id)->first();

        $result = $this->paySlipDataFormat($month_of_salary, $employee_id);

        if ($result != []) {
            $pdf = PDF::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf', $result);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("payslip.pdf");
        } else {
            return $this->controller->custom_error('Payslip Not Found ! try different month');
        }
    }

    public function paySlipDataFormat($month_of_salary, $employee_id)
    {
        $printHeadSetting = PrintHeadSetting::first();
        $data = [];

        $salaryDetails    = SalaryDetails::select('salary_details.*', 'employee.employee_id', 'employee.department_id', 'employee.designation_id', 'department.department_name', 'designation.designation_name', 'employee.first_name', 'employee.last_name', 'pay_grade.pay_grade_name', 'employee.date_of_joining')
            ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('pay_grade', 'pay_grade.pay_grade_id', 'employee.pay_grade_id')
            // ->where('salary_details_id', $id)
            ->where('salary_details.month_of_salary', (string)$month_of_salary)->where('salary_details.employee_id', $employee_id)
            ->first();

        if ($salaryDetails) {
            $salaryDetailsToAllowance = SalaryDetailsToAllowance::join('allowance', 'allowance.allowance_id', 'salary_details_to_allowance.allowance_id')
                ->where('salary_details_id', $salaryDetails['salary_details_id'])->get();

            $salaryDetailsToDeduction = SalaryDetailsToDeduction::join('deduction', 'deduction.deduction_id', 'salary_details_to_deduction.deduction_id')
                ->where('salary_details_id', $salaryDetails['salary_details_id'])->get();

            $salaryDetailsToLeave = SalaryDetailsToLeave::select('salary_details_to_leave.*', 'leave_type.leave_type_name')
                ->join('leave_type', 'leave_type.leave_type_id', 'salary_details_to_leave.leave_type_id')
                ->where('salary_details_id', $salaryDetails['salary_details_id'])->get();

            $monthAndYear = explode('-', $salaryDetails->month_of_salary);
            $start_year   = $monthAndYear[0] . '-01';
            $end_year     = $salaryDetails->month_of_salary;

            $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
                ->where('status', 1)
                ->where('employee_id', $salaryDetails->employee_id)
                ->whereBetween('month_of_salary', [$start_year, $end_year])
                ->first();

            $data = [
                'salaryDetails'            => $salaryDetails,
                'salaryDetailsToAllowance' => $salaryDetailsToAllowance,
                'salaryDetailsToDeduction' => $salaryDetailsToDeduction,
                'paySlipId'                => $salaryDetails['salary_details_id'],
                'financialYearTax'         => $financialYearTax,
                'salaryDetailsToLeave'     => $salaryDetailsToLeave,
                'printHeadSetting'         => $printHeadSetting,
            ];

            return $data;
        } else {
            return $data;
        }
    }
}
