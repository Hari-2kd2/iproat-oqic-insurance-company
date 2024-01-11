<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\LeaveStatus;
use App\Lib\Enumerations\UserStatus;
use App\Model\CompanyAddressSetting;
use App\Model\Employee;
use App\Model\LeaveApplication;
use App\Model\OvertimeRule;
use App\Model\PrintHeadSetting;
use App\Model\SalaryDetails;
use App\Model\SalaryDetailsToAllowance;
use App\Model\SalaryDetailsToDeduction;
use App\Model\SalaryDetailsToLeave;
use App\Repositories\AttendanceRepository;
use App\Repositories\CommonRepository;
use App\Repositories\PayrollRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;

class GenerateSalarySheet extends Controller
{

    protected $commonRepository;
    protected $payrollRepository;
    protected $attendanceRepository;

    public function __construct(AttendanceRepository $attendanceRepository, CommonRepository $commonRepository, PayrollRepository $payrollRepository)
    {
        $this->commonRepository     = $commonRepository;
        $this->payrollRepository    = $payrollRepository;
        $this->attendanceRepository = $attendanceRepository;
    }

    public function index(Request $request)
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'payGrade', 'hourlySalaries']);
        }])->orderBy('salary_details_id', 'DESC')->paginate(10);
        if (request()->ajax()) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'payGrade', 'hourlySalaries']);
            }])->orderBy('salary_details_id', 'DESC');

            if ($request->monthField != '') {
                $results->where('month_of_salary', $request->monthField);
            }

            if ($request->status != '') {
                $results->where('status', $request->status);
            }
            if ($request->payment_method != '') {
                $results->where('payment_method', $request->payment_method);
            }

            $results = $results->paginate(10);

            return View('admin.payroll.salarySheet.pagination', compact('results'))->render();
        }
        $departmentList = $this->commonRepository->departmentList();
        return view('admin.payroll.salarySheet.salaryDetails', ['results' => $results, 'departmentList' => $departmentList]);
    }

    public function monthSalary(Request $request)
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with('payGrade');
        }])->where('month_of_salary', $request->month)->get();

        return view('admin.payroll.salarySheet.salaryDetails', ['results' => $results]);
    }

    public function create()
    {
        $employeeList = $this->commonRepository->employeeList();
        return view('admin.payroll.salarySheet.generateSalarySheet', ['employeeList' => $employeeList]);
    }

    public function calculateEmployeeSalary(Request $request)
    {

        $query = DB::select("SELECT temp.* FROM (
                    SELECT DATE_FORMAT(date,'%Y-%m') AS yearAndMonth,view_employee_in_out_data.finger_print_id,employee.employee_id FROM view_employee_in_out_data
                    JOIN employee ON employee.finger_id = view_employee_in_out_data.finger_print_id
                    ) AS temp WHERE yearAndMonth='$request->month' AND employee_id = $request->employee_id");

        if (count($query) <= 0) {
            return redirect('generateSalarySheet/create')->with('error', 'No attendance found.');
        }

        $queryResult = SalaryDetails::where('employee_id', $request->employee_id)->where('month_of_salary', $request->month)->count();
        if ($queryResult > 0) {
            return redirect('generateSalarySheet')->with('error', 'Salary already generated for this month.');
        }

        $employeeList    = $this->commonRepository->employeeList();
        $employeeDetails = Employee::with('payGrade', 'hourlySalaries', 'department', 'designation')->where('employee_id', $request->employee_id)->first();
        if ($employeeDetails->pay_grade_id != 0) {
            $employeeAllInfo = [];
            $allowance       = [];
            $deduction       = [];
            $tax             = 0;

            $from_date = $request->month . "-01";
            $to_date   = date('Y-m-t', strtotime($from_date));

            $leaveRecord = LeaveApplication::select('leave_type.leave_type_id', 'leave_type_name', 'number_of_day', 'application_from_date', 'application_to_date')
                ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
                ->where('status', LeaveStatus::$APPROVE)
                ->where('application_from_date', '>=', $from_date)
                ->where('application_to_date', '<=', $to_date)
                ->where('employee_id', $request->employee_id)
                ->get();

            $monthAndYear = explode('-', $request->month);
            $start_year   = $monthAndYear[0] . '-01';
            $end_year     = $monthAndYear[0] . '-12';

            $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
                ->where('status', 1)
                ->where('employee_id', $request->employee_id)
                ->whereBetween('month_of_salary', [$start_year, $end_year])
                ->first();

            $allowance = $this->payrollRepository->calculateEmployeeAllowance($employeeDetails->payGrade->basic_salary, $employeeDetails->pay_grade_id);

            $deduction = $this->payrollRepository->calculateEmployeeDeduction($employeeDetails->payGrade->basic_salary, $employeeDetails->pay_grade_id);

            $advanceDeduction = $this->payrollRepository->calculateEmployeeAdvanceDeduction($request->employee_id);

            $monthlyDeduction = $this->payrollRepository->calculateEmployeeMonthlyDeduction($request->employee_id, $request->month);

            $monthlyOvertimeReduction = $this->attendanceRepository->getEmployeeMonthlyAttendance(dateConvertFormtoDB($from_date), dateConvertFormtoDB($to_date), $request->employee_id);
            $overtimeRule             = OvertimeRule::select('amount_of_deduction')->first();
            $tax                      = $this->payrollRepository->calculateEmployeeTax(
                $employeeDetails->payGrade->gross_salary,
                $employeeDetails->payGrade->basic_salary,
                $employeeDetails->date_of_birth,
                $employeeDetails->gender,
                $employeeDetails->pay_grade_id
            );
            $employeeAllInfo = $this->payrollRepository->getEmployeeOtmAbsLvLtAndWokDaysManualWay(
                $request->employee_id, $request->month,
                $employeeDetails->payGrade->overtime_rate,
                $employeeDetails->payGrade->basic_salary
            );

            $data = [
                'employeeList'             => $employeeList,
                'allowances'               => $allowance,
                'deductions'               => $deduction,
                'advanceDeduction'         => $advanceDeduction,
                'monthlyDeduction'         => $monthlyDeduction,
                'monthlyOvertimeReduction' => $monthlyOvertimeReduction,
                'overtimeRule'             => $overtimeRule,
                'tax'                      => $tax['monthlyTax'],
                'taxAbleSalary'            => $tax['taxAbleSalary'],
                'employee_id'              => $request->employee_id,
                'month'                    => $request->month,
                'employeeAllInfo'          => $employeeAllInfo,
                'employeeDetails'          => $employeeDetails,
                'leaveRecords'             => $leaveRecord,
                'financialYearTax'         => $financialYearTax,
                'employeeGrossSalary'      => $employeeDetails->payGrade->gross_salary,
            ];
        } else {
            $employeeHourlySalary = $this->payrollRepository->getEmployeeHourlySalary($request->employee_id, $request->month, $employeeDetails->hourlySalaries->hourly_rate);

            $data = [
                'employeeList'     => $employeeList,
                'hourly_rate'      => $employeeDetails->hourlySalaries->hourly_rate,
                'employee_id'      => $request->employee_id,
                'month'            => $request->month,
                'totalWorkingHour' => $employeeHourlySalary['totalWorkingHour'],
                'totalSalary'      => $employeeHourlySalary['totalSalary'],
                'employeeDetails'  => $employeeDetails,

            ];
            return view('admin.payroll.salarySheet.generateHourlySalarySheet', $data);
        }
        return view('admin.payroll.salarySheet.generateSalarySheet', $data);
    }

    public function store(Request $request)
    {
        $input               = $request->all();
        $input['created_by'] = Auth::user()->user_id;
        $input['updated_by'] = Auth::user()->user_id;

        try {
            DB::beginTransaction();

            $parentData                       = SalaryDetails::create($input);
            $employeeSalaryDetailsToAllowance = $this->makeEmployeeSalaryDetailsToAllowanceDataFormat($request->all(), $parentData->salary_details_id);

            if (count($employeeSalaryDetailsToAllowance) > 0) {
                SalaryDetailsToAllowance::insert($employeeSalaryDetailsToAllowance);
            }

            $employeeSalaryDetailsToDeduction = $this->makeEmployeeSalaryDetailsToDeductionDataFormat($request->all(), $parentData->salary_details_id);
            if (count($employeeSalaryDetailsToDeduction) > 0) {
                SalaryDetailsToDeduction::insert($employeeSalaryDetailsToDeduction);
            }

            $employeeSalaryDetailsToLeave = $this->makeEmployeeSalaryDetailsToLeaveDataFormat($request->all(), $parentData->salary_details_id);
            if (count($employeeSalaryDetailsToLeave) > 0) {
                SalaryDetailsToLeave::insert($employeeSalaryDetailsToLeave);
            }

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            DB::rollback();
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return redirect('generateSalarySheet')->with('success', 'Salary Generate successfully.');
        } else {
            return redirect('generateSalarySheet')->with('error', 'Something Error Found !, Please try again.');
        }

    }

    public function makeEmployeeSalaryDetailsToAllowanceDataFormat($data, $salary_details_id)
    {
        $allowanceData = [];
        if (isset($data['allowance_id'])) {
            for ($i = 0; $i < count($data['allowance_id']); $i++) {
                $allowanceData[$i] = [
                    'salary_details_id'   => $salary_details_id,
                    'allowance_id'        => $data['allowance_id'][$i],
                    'amount_of_allowance' => $data['amount_of_allowance'][$i],
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ];
            }
        }
        return $allowanceData;
    }

    public function makeEmployeeSalaryDetailsToDeductionDataFormat($data, $salary_details_id)
    {
        $deductionData = [];
        if (isset($data['deduction_id'])) {
            for ($i = 0; $i < count($data['deduction_id']); $i++) {
                $deductionData[$i] = [
                    'salary_details_id'   => $salary_details_id,
                    'deduction_id'        => $data['deduction_id'][$i],
                    'amount_of_deduction' => $data['amount_of_deduction'][$i],
                    'created_at'          => Carbon::now(),
                    'updated_at'          => Carbon::now(),
                ];
            }
        }
        return $deductionData;
    }

    public function makeEmployeeSalaryDetailsToLeaveDataFormat($data, $salary_details_id)
    {
        $leaveData = [];
        if (isset($data['num_of_day'])) {
            for ($i = 0; $i < count($data['num_of_day']); $i++) {
                $leaveData[$i] = [
                    'salary_details_id' => $salary_details_id,
                    'num_of_day'        => $data['num_of_day'][$i],
                    'leave_type_id'     => $data['leave_type_id'][$i],
                    'created_at'        => Carbon::now(),
                    'updated_at'        => Carbon::now(),
                ];
            }
        }
        return $leaveData;
    }

    public function makePayment(Request $request)
    {
        $data['status']         = 1;
        $data['comment']        = $request->comment;
        $data['payment_method'] = $request->payment_method;
        $data['created_by']     = Auth::user()->user_id;
        $data['updated_by']     = Auth::user()->user_id;
        $data['created_at']     = Carbon::now();
        $data['updated_at']     = Carbon::now();
        try {
            SalaryDetails::where('salary_details_id', $request->salary_details_id)->update($data);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function generatePayslip($id)
    {
        $paySlipId = $id;
        $ifHourly  = SalaryDetails::with(['employee' => function ($q) {
            $q->with(['hourlySalaries', 'department', 'designation']);
        }])->where('salary_details_id', $paySlipId)->first();

        if ($ifHourly->action == 'monthlySalary') {
            $paySlipDataFormat = $this->paySlipDataFormat($paySlipId);
        } else {
            $companyAddress = CompanyAddressSetting::first();
            $data           = [
                'salaryDetails'  => $ifHourly,
                'companyAddress' => $companyAddress,
                'paySlipId'      => $id,
            ];
            return view('admin.payroll.salarySheet.hourlyPaySlip', $data);
        }

        return view('admin.payroll.salarySheet.monthlyPaySlip', $paySlipDataFormat);
    }

    public function paySlipDataFormat($id)
    {
        $printHeadSetting = PrintHeadSetting::first();
        $salaryDetails    = SalaryDetails::select('salary_details.*', 'employee.employee_id', 'employee.department_id', 'employee.designation_id', 'department.department_name', 'designation.designation_name', 'employee.first_name', 'employee.last_name', 'pay_grade.pay_grade_name', 'employee.date_of_joining')
            ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->join('pay_grade', 'pay_grade.pay_grade_id', 'employee.pay_grade_id')
            ->where('salary_details_id', $id)->first();

        $salaryDetailsToAllowance = SalaryDetailsToAllowance::join('allowance', 'allowance.allowance_id', 'salary_details_to_allowance.allowance_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToDeduction = SalaryDetailsToDeduction::join('deduction', 'deduction.deduction_id', 'salary_details_to_deduction.deduction_id')
            ->where('salary_details_id', $id)->get();

        $salaryDetailsToLeave = SalaryDetailsToLeave::select('salary_details_to_leave.*', 'leave_type.leave_type_name')
            ->join('leave_type', 'leave_type.leave_type_id', 'salary_details_to_leave.leave_type_id')
            ->where('salary_details_id', $id)->get();

        $monthAndYear = explode('-', $salaryDetails->month_of_salary);
        $start_year   = $monthAndYear[0] . '-01';
        $end_year     = $salaryDetails->month_of_salary;

        $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
            ->where('status', 1)
            ->where('employee_id', $salaryDetails->employee_id)
            ->whereBetween('month_of_salary', [$start_year, $end_year])
            ->first();

        return $data = [
            'salaryDetails'            => $salaryDetails,
            'salaryDetailsToAllowance' => $salaryDetailsToAllowance,
            'salaryDetailsToDeduction' => $salaryDetailsToDeduction,
            'paySlipId'                => $id,
            'financialYearTax'         => $financialYearTax,
            'salaryDetailsToLeave'     => $salaryDetailsToLeave,
            'printHeadSetting'         => $printHeadSetting,
        ];
    }

    public function downloadPayslip($id)
    {
        $payslipId = $id;
        $ifHourly  = SalaryDetails::with(['employee' => function ($q) {
            $q->with(['hourlySalaries', 'department', 'designation']);
        }])->where('salary_details_id', $payslipId)->first();

        if ($ifHourly->action == 'monthlySalary') {
            $result = $this->paySlipDataFormat($payslipId);
        } else {
            $printHeadSetting = PrintHeadSetting::first();
            $data             = [
                'salaryDetails'    => $ifHourly,
                'printHeadSetting' => $printHeadSetting,
            ];
//          return view('admin.payroll.salarySheet.hourlyPaySlipPdf',$data);
            $pdf = PDF::loadView('admin.payroll.salarySheet.hourlyPaySlipPdf', $data);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("payslip.pdf");
        }

        $pdf = PDF::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf', $result);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("payslip.pdf");
    }

    public function downloadMyPayroll()
    {
        $printHeadSetting = PrintHeadSetting::first();
        $results          = SalaryDetails::with(['employee' => function ($query) {
            $query->with('payGrade');
        }])->where('status', 1)->where('employee_id', (session('logged_session_data.employee_id')))->orderBy('salary_details_id', 'DESC')->get();

        $data = [
            'printHead' => $printHeadSetting,
            'results'   => $results,
        ];

        $pdf = PDF::loadView('admin.payroll.report.pdf.myPayrollPdf', $data);

        $pdf->setPaper('A4', 'landscape');
        return $pdf->download("my-payroll-Pdf.pdf");

    }

    public function paymentHistory(Request $request)
    {
        $results = '';
        if ($request->month) {
            $results = SalaryDetails::select('salary_details.basic_salary', 'salary_details.gross_salary', 'salary_details.month_of_salary', DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS fullName'),
                'employee.photo', 'pay_grade.pay_grade_name', 'hourly_salaries.hourly_grade', 'department.department_name')
                ->join('employee', 'employee.employee_id', 'salary_details.employee_id')
                ->join('department', 'department.department_id', 'employee.department_id')
                ->leftJoin('pay_grade', 'pay_grade.pay_grade_id', 'employee.pay_grade_id')
                ->leftJoin('hourly_salaries', 'hourly_salaries.hourly_salaries_id', 'employee.hourly_salaries_id')
                ->where('salary_details.status', 1)
                ->where('salary_details.month_of_salary', $request->month)
                ->orderBy('salary_details_id', 'DESC')
                ->get();
        }

        return view('admin.payroll.report.paymentHistory', ['results' => $results, 'month' => $request->month]);
    }

    public function myPayroll()
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with('payGrade');
        }])->where('status', 1)->where('employee_id', (session('logged_session_data.employee_id')))->orderBy('salary_details_id', 'DESC')->get();
        return view('admin.payroll.report.myPayroll', ['results' => $results]);
    }

    public function payslip(Request $request)
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department', 'payGrade', 'hourlySalaries']);
        }])->orderBy('salary_details_id', 'DESC')->paginate(10);

        if (request()->ajax()) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department', 'payGrade', 'hourlySalaries']);
            }])->orderBy('salary_details_id', 'DESC');

            if ($request->monthField != '') {
                $results->where('status', 1)->where('month_of_salary', $request->monthField);
            }

            $results = $results->paginate(10);

            return View('admin.payroll.salarySheet.pagination', compact('results'))->render();
        }

        $departmentList = $this->commonRepository->departmentList();
        return view('admin.payroll.salarySheet.downloadPayslip', ['results' => $results, 'departmentList' => $departmentList]);
    }

    public function payment(Request $request)
    {
        //Input items of form
        $input = Input::all();
        //get API Configuration
        $api = new Api("rzp_test_EzVAI0XNlc8bPq", "bxvBaGVNPKuCj4qmJuiTJyoK");
        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));

                $data['status']         = 1;
                $data['comment']        = $request->comment;
                $data['payment_method'] = "RazorPay";
                $data['created_by']     = Auth::user()->user_id;
                $data['updated_by']     = Auth::user()->user_id;
                $data['created_at']     = Carbon::now();
                $data['updated_at']     = Carbon::now();

                if ($response) {
                    $store = SalaryDetails::where('salary_details_id', $request->salary_details_id)->update($data);
                }
            } catch (\Exception $e) {
                return $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back();
            }

            // Do something here for store payment details in database...
        }

        Session::put('success', 'Payment successful, your order will be despatched in the next 48 hours.');
        return redirect()->back();
    }

    public function calculateEmployeeSalaryDetails($month)
    {
        $employeeIds = Employee::where('status', UserStatus::$ACTIVE)->orderBy('employee_id', 'asc')->pluck('employee_id');
        // $month_of_salary = $request->month;
        $month = monthConvertFormtoDB(\Carbon\Carbon::createFromFormat('Y-m', $month));
        // $month = monthConvertFormtoDB(Carbon::now()->subMonth(2)->format('Y-m'));
        // dd($month);
        foreach ($employeeIds as $key => $id) {
            // dd($employeeIds);

            // $employeeList = $this->employeeList($id);
            // // dd($employeeList);

            $employeeDetails = Employee::with('payGrade', 'hourlySalaries', 'department', 'designation')->where('employee_id', $id)->first();
            // dd($employeeDetails);

            if ($employeeDetails->pay_grade_id != 0) {
                $employeeAllInfo = [];
                $allowance       = [];
                $deduction       = [];
                $tax             = 0;

                $from_date = $month . "-01";
                $to_date   = date('Y-m-t', strtotime($from_date));

                $employeeList = $this->employeeList($id, $from_date, $to_date);

                $leaveRecord = LeaveApplication::select('leave_type.leave_type_id', 'leave_type_name', 'number_of_day', 'application_from_date', 'application_to_date')
                    ->join('leave_type', 'leave_type.leave_type_id', 'leave_application.leave_type_id')
                    ->where('status', LeaveStatus::$APPROVE)
                    ->where('application_from_date', '>=', $from_date)
                    ->where('application_to_date', '<=', $to_date)
                    ->where('employee_id', $id)
                    ->get();
                // dd($leaveRecord);

                $monthAndYear = explode('-', $month);
                $start_year   = $monthAndYear[0] . '-01';
                $end_year     = $monthAndYear[0] . '-12';

                $financialYearTax = SalaryDetails::select(DB::raw('SUM(tax) as totalTax'))
                    ->where('status', 1)
                    ->where('employee_id', $id)
                    ->whereBetween('month_of_salary', [$start_year, $end_year])
                    ->first();

                $allowance = $this->payrollRepository->calculateEmployeeAllowance($employeeDetails->payGrade->basic_salary, $employeeDetails->pay_grade_id);

                $deduction = $this->payrollRepository->calculateEmployeeDeduction($employeeDetails->payGrade->basic_salary, $employeeDetails->pay_grade_id);

                $advanceDeduction = $this->payrollRepository->calculateEmployeeAdvanceDeduction($id);

                $monthlyDeduction = $this->payrollRepository->calculateEmployeeMonthlyDeduction($id, $month);

                $tax = $this->payrollRepository->calculateEmployeeTax(
                    $employeeDetails->payGrade->gross_salary,
                    $employeeDetails->payGrade->basic_salary,
                    $employeeDetails->date_of_birth,
                    $employeeDetails->gender,
                    $employeeDetails->pay_grade_id
                );
                $employeeAllInfo = $this->payrollRepository->getEmployeeOtmAbsLvLtAndWokDays(
                    $id, $month,
                    $employeeDetails->payGrade->overtime_rate,
                    $employeeDetails->payGrade->basic_salary
                );

                $input = [
                    'employeeList'        => $employeeList,
                    'allowances'          => $allowance,
                    'deductions'          => $deduction,
                    'advanceDeduction'    => $advanceDeduction,
                    'monthlyDeduction'    => $monthlyDeduction,
                    'tax'                 => $tax['monthlyTax'],
                    'taxAbleSalary'       => $tax['taxAbleSalary'],
                    'employee_id'         => $id,
                    'month'               => $month,
                    'employeeAllInfo'     => $employeeAllInfo,
                    'employeeDetails'     => $employeeDetails,
                    'leaveRecords'        => $leaveRecord,
                    'financialYearTax'    => $financialYearTax,
                    'employeeGrossSalary' => $employeeDetails->payGrade->gross_salary,
                ];
                // dd($input);

            } else {
                $employeeHourlySalary = $this->payrollRepository->getEmployeeHourlySalary($id, $month, $employeeDetails->hourlySalaries->hourly_rate);
                // dd($employeeHourlySalary);
                // dd($employeeDetails->hourlySalaries->hourly_rate);
                $from_date = $month . "-01";
                $to_date   = date('Y-m-t', strtotime($from_date));

                $employeeList = $this->employeeList($id, $from_date, $to_date);

                $input = [
                    'employeeList'     => $employeeList,
                    'hourly_rate'      => $employeeDetails->hourlySalaries->hourly_rate,
                    'employee_id'      => $id,
                    'month'            => $month,
                    'totalWorkingHour' => $employeeHourlySalary['totalWorkingHour'],
                    'totalSalary'      => $employeeHourlySalary['totalSalary'],
                    'employeeDetails'  => $employeeDetails,
                ];
                $data[$key] = $input;
            }
            $data[$key] = $input;
        }
        // dd($data);
        // return $employeeDetails->payGrade->basic_salary;
        return $data;
    }

    public function employeeList($id, $from_date, $to_date)
    {
        $results = Employee::where('employee_id', $id)->where('status', 1)->join('view_employee_in_out_data', 'view_employee_in_out_data.finger_print_id', '=', 'employee.finger_id')->whereBetween('date', [$from_date, $to_date])->get();
        foreach ($results as $key => $value) {
            $options[$value->employee_id] = $value->first_name . ' ' . $value->last_name;
        }
        // dd($options);
        return $options;
    }
    public function employeeIdList()
    {
        $results = Employee::where('status', 1)->orderBy('first_name', 'asc')->get();
        foreach ($results as $key => $value) {
            $options[$value->employee_id] = $value->first_name . ' ' . $value->last_name;
        }
        return $options;
    }

    public function generateSalarySheetToAllEmployees(Request $request)
    {
        try {
            $current_month = Carbon::today()->format('Y-m');
            $past_month    = Carbon::today()->subMonth(1)->format('Y-m');
            if (isset($request->month) && $current_month != $request->month && $past_month == $request->month) {
                // dump(date('Y-m'));
                // dump($request->month);
                // dd(Carbon::today()->subMonth(1)->format('Y-m'));

                $month     = monthConvertFormtoDB(\Carbon\Carbon::createFromFormat('Y-m', $request->month));
                $from_date = $month . "-01";
                $to_date   = date('Y-m-t', strtotime($from_date));
                // dd($month);
                $bug = \null;
                DB::beginTransaction();
                $employeeResults = $this->calculateEmployeeSalaryDetails($month);
                // dd($employeeResults);

                foreach ($employeeResults as $key => $value) {
                    // dd($employeeResults);
                    $time                = date('Y-m-d H:i:s');
                    $employee_id         = $value['employee_id'];
                    $finger_id           = Employee::where('employee_id', $employee_id)->select('finger_id')->first();
                    $finger_id           = $finger_id['finger_id'];
                    $advanceDeduction    = isset($value['advanceDeduction']['totalDeduction']) ? $value['advanceDeduction']['totalDeduction'] : 0;
                    $monthlyDeduction    = isset($value['monthlyDeduction']['totalMonthlyDeduction']) ? $value['monthlyDeduction']['totalMonthlyDeduction'] : 0;
                    $tax                 = $value['tax'] ?? 0;
                    $basicSalary         = isset($value['employeeDetails']->payGrade->basic_salary) ? $value['employeeDetails']->payGrade->basic_salary : 0;
                    $allowances          = isset($value['allowances']['totalAllowance']) ? $value['allowances']['totalAllowance'] : 0;
                    $deductions          = isset($value['deductions']['totalDeduction']) ? $value['deductions']['totalDeduction'] : 0;
                    $totalOvertimeAmount = isset($value['employeeAllInfo']['totalOvertimeAmount']) ? $value['employeeAllInfo']['totalOvertimeAmount'] : 0;
                    $totalAbsenceAmount  = isset($value['employeeAllInfo']['totalAbsenceAmount']) ? $value['employeeAllInfo']['totalAbsenceAmount'] : 0;
                    $totalLateAmount     = isset($value['employeeAllInfo']['totalLateAmount']) ? $value['employeeAllInfo']['totalLateAmount'] : 0;
                    $netSalaryMonthly    = (($basicSalary + $totalOvertimeAmount) - ($allowances + $deductions + $totalAbsenceAmount + $totalLateAmount + $advanceDeduction + $monthlyDeduction));
                    $totalWorkHour       = isset($value['totalSalary']['totalWorkingHour']) ? $value['totalSalary']['totalWorkingHour'] : 0;
                    $netHourlySalary     = isset($value['totalSalary']['totalSalary']) ? $value['totalSalary']['totalSalary'] : 0;
                    $hourlyRate          = isset($value['employeeDetails']->hourlySalaries->hourly_rate) ? $value['employeeDetails']->hourlySalaries->hourly_rate : 0;
                    $totalLate           = isset($value['employeeAllInfo']['totalLate']) ? $value['employeeAllInfo']['totalLate'] : 0;
                    $totalAbsence        = isset($value['employeeAllInfo']['totalAbsence']) ? $value['employeeAllInfo']['totalAbsence'] : 0;
                    $overtimeRate        = isset($value['employeeAllInfo']['overtime_rate']) ? $value['employeeAllInfo']['overtime_rate'] : 0;
                    $oneDaysSalary       = isset($value['employeeAllInfo']['oneDaysSalary']) ? $value['employeeAllInfo']['oneDaysSalary'] : 0;
                    $totalOverTimeHour   = isset($value['employeeAllInfo']['totalOverTimeHour']) ? $value['employeeAllInfo']['totalOverTimeHour'] : 0;
                    $totalPresent        = isset($value['employeeAllInfo']['totalPresent']) ? $value['employeeAllInfo']['totalPresent'] : 0;
                    $totalLeave          = isset($value['employeeAllInfo']['totalLeave']) ? $value['employeeAllInfo']['totalLeave'] : 0;
                    $totalWorkingDays    = isset($value['employeeAllInfo']['totalWorkingDays']) ? $value['employeeAllInfo']['totalWorkingDays'] : 0;
                    $taxAbleSalary       = isset($value['taxAbleSalary']) ? $value['taxAbleSalary'] : 0;
                    // dd($netHourlySalary);

                    $query = DB::table('view_employee_in_out_data')->whereBetween('date', [$from_date, $to_date])
                        ->where('finger_print_id', $finger_id)->first();
                    // dd($query);

                    $queryResult = SalaryDetails::where('employee_id', $employee_id)->where('month_of_salary', $month)->first();
                    // return($queryResult);

                    // $ifExists    = json_decode(DB::table(DB::raw("(SELECT salary_details.*"))
                    //         ->select('salary_details.salary_detail_id')
                    //         ->where('salary_details.employee_id', $employee_id)
                    //         ->get()->toJson(), true);
                    // dd($employee_id);
                    // DB::table('salary_details')->whereIn('salary_detail_id', array_values($ifExists))->delete();

                    if ($queryResult) {
                        $bug = 1;
                    } elseif (!$query) {
                        $bug = 2;
                    } else {
                        $inputData = [
                            'employee_id'           => $employee_id,
                            'month_of_salary'       => monthConvertFormtoDB($month),
                            'basic_salary'          => $basicSalary,
                            'total_allowance'       => $allowances,
                            'total_deduction'       => $deductions,
                            'total_late'            => $totalLate,
                            'total_late_amount'     => $totalLateAmount,
                            'total_absence'         => $totalAbsence,
                            'total_absence_amount'  => $totalAbsenceAmount,
                            'overtime_rate'         => $overtimeRate,
                            'per_day_salary'        => $oneDaysSalary,
                            'total_over_time_hour'  => $totalOverTimeHour,
                            'total_overtime_amount' => $totalOvertimeAmount,
                            'total_present'         => $totalPresent,
                            'total_leave'           => $totalLeave,
                            'total_working_days'    => $totalWorkingDays,
                            'net_salary'            => $netSalaryMonthly != 0 ? $netSalaryMonthly : $netHourlySalary,
                            'hourly_rate'           => $hourlyRate,
                            'tax'                   => $tax,
                            'taxable_salary'        => $taxAbleSalary,
                            'gross_salary'          => $netSalaryMonthly != 0 ? $netSalaryMonthly : $netHourlySalary,
                            'created_by'            => Auth::user()->user_id,
                            'updated_by'            => Auth::user()->user_id,
                            'status'                => 0,
                            'comment'               => \null,
                            'payment_method'        => \null,
                            'action'                => 'monthlySalary',
                            'created_at'            => $time,
                            'updated_at'            => $time,
                        ];
                        DB::table('salary_details')->insert([$inputData]);
                        // dd($inputData);
                        DB::commit();
                        $bug = 0;
                    }
                }
            } elseif ($current_month == $request->month) {
                $bug = 4;
            } elseif (!isset($request->month)) {
                $bug = 3;
            } else {
                $bug = 5;
            }

        } catch (\Exception $e) {
            DB::rollback();
            $bug     = 3;
            $message = $e->getMessage();
        }

        switch ($bug) {
            case 0:
                return redirect('generateSalarySheet')->with('success', 'Salary Generate successfully.');
                break;
            case 1:
                return redirect('generateSalarySheet')->with('error', 'Salary already generated for this month.');
                break;
            case 2:
                return redirect('generateSalarySheet/create')->with('error', 'No attendance found.');
                break;
            case 3:
                return redirect('generateSalarySheet/create')->with('error', 'Please select month and try again.');
                break;
            case 4:
                return redirect('generateSalarySheet/create')->with('error', 'Salary cannot be generated for ongoing month ! , Please select correct month.');
                break;
            case 5:
                return redirect('generateSalarySheet/create')->with('error', 'Salary cannot be generated for earlier months ! , Please select correct month.');
                break;
            default:
                return redirect('generateSalarySheet')->with('error', 'Something Error Found !, Please try again.');
        }

    }

    public function makePaymentToAllEmployees(Request $request)
    {

        try {
            $current_month = Carbon::today()->format('Y-m');
            $past_month    = Carbon::today()->subMonth(1)->format('Y-m');
            if (isset($request->month) && $current_month != $request->month && $past_month == $request->month) {
                // dd($current_month != $request->month,$past_month == $request->month);
                $month         = monthConvertFormtoDB(\Carbon\Carbon::createFromFormat('Y-m', $request->month));
                $salaryDetails = Employee::Join('salary_details', 'salary_details.employee_id', '=', 'employee.employee_id')
                    ->where('salary_details.month_of_salary', $month)
                    ->select('salary_details.employee_id', 'salary_details.salary_details_id')
                    ->orderBy('salary_details.salary_details_id')->get();
                // dd($salaryDetails);
                $bug     = 2;
                $message = 'No Data Found';
                DB::beginTransaction();
                if (!empty($salaryDetails)) {
                    foreach ($salaryDetails as $key => $value) {
                        // dd($value);
                        // dd($value['employee_id']);
                        $queryResult = SalaryDetails::where('employee_id', $value['employee_id'])->where('month_of_salary', $month)->first();
                        // dd($queryResult);
                        if ($queryResult) {
                            $inputData = [
                                'status'         => 1,
                                'comment'        => "None",
                                'payment_method' => "RazorPay",
                                'created_by'     => Auth::user()->user_id,
                                'updated_by'     => Auth::user()->user_id,
                                'created_at'     => Carbon::now(),
                                'updated_at'     => Carbon::now(),
                            ];
                            SalaryDetails::where('salary_details_id', $value['salary_details_id'])->where('employee_id', $value['employee_id'])->update($inputData);
                            DB::commit();
                            $bug = 0;

                        }
                    }
                }
            } else {
                $bug = 3;
            }
        } catch (\Exception $e) {
            DB::rollback();
            $bug     = 4;
            $message = $e->getMessage();
        }

        switch ($bug) {
            case 0:
                return redirect('generateSalarySheet')->with('success', 'Payment successfully.');
                break;
            case 1:
                return redirect('generateSalarySheet/create')->with('error', 'Please select month and try again.');
                break;
            case 2:
                return redirect('generateSalarySheet/create')->with('error', $message . ' !, Please try again.');
                break;
            case 3:
                return redirect('generateSalarySheet/create')->with('error', 'Salary detials not avaliable for this month.');
                break;
            case 4:
                return redirect('generateSalarySheet/create')->with('error', $message);
                break;
            default:
                return redirect('generateSalarySheet')->with('error', 'Something Error Found !, Please try again.');
        }

    }

    public function downloadPayslipForAllEmployee()
    {
        $salaryDetails = Employee::Join('salary_details', 'salary_details.employee_id', '=', 'employee.employee_id')
            ->where('salary_details.month_of_salary', '2022-01')
            ->orderBy('salary_details.salary_details_id')->pluck('salary_details.salary_details_id');

        foreach ($salaryDetails as $key => $payslipId) {
            $ifHourly = SalaryDetails::with(['employee' => function ($q) {
                $q->with(['hourlySalaries', 'department', 'designation']);
            }])->where('salary_details_id', $payslipId)->first();

            if ($ifHourly->action == 'monthlySalary') {
                $result = $this->paySlipDataFormat($payslipId);
            } else {
                $printHeadSetting = PrintHeadSetting::first();
                $data             = [
                    'salaryDetails'    => $ifHourly,
                    'printHeadSetting' => $printHeadSetting,
                ];

                $pdf = PDF::loadView('admin.payroll.salarySheet.hourlyPaySlipPdf', $data);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download("payslip.pdf");
            }

            $pdf = PDF::loadView('admin.payroll.salarySheet.monthlyPaySlipPdf', $result);
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download("payslip.pdf");
        }

    }

    public function detail($month)
    {
        $employeeResults = $this->calculateEmployeeSalaryDetails($month);
        return ($employeeResults);
    }

}
