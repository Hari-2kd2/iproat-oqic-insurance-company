<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use App\Model\Employee;
use App\Model\PrintHeadSetting;
use App\Model\SalaryDetails;
use App\Repositories\PayrollRepository;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UploadSalaryDetailController extends Controller
{
    protected $_attendanceRepository;

    public function __construct(PayrollRepository $payrollRepository)
    {
        $this->payrollRepository = $payrollRepository;
    }
    public function index(Request $request)
    {
        $results = SalaryDetails::with(['employee' => function ($query) {
            $query->with(['department']);
        }])->where('salary_details.status', 1)->orderBy('month_of_salary', 'DESC')->groupBy('salary_details_id')->paginate(20);
        if (request()->ajax()) {

            $results = SalaryDetails::with(['employee' => function ($query) {
                $query->with(['department']);
            }])->where('salary_details.status', 1)->orderBy('month_of_salary', 'DESC')->groupBy('salary_details_id');

            if ($request->monthField != '') {
                $results->where('month_of_salary', $request->monthField);
            }

            $results = $results->paginate(20);

            return View('admin.payroll.uploadSalaryDetails.pagination', compact('results'))->render();
        }

        return view('admin.payroll.uploadSalaryDetails.uploadSalaryDetails', ['results' => $results, 'month' => $request->monthField]);
    }

    // public function index(Request $request)
    // {
    //     $results = [];
    //     if ($_POST) {
    //         $results = $this->payrollRepository->getEmployeeMonthlySalaryDetails( \Carbon\Carbon::createFromFormat('Y-m', $request->month));
    //         $results = $results->paginate(10);
    //     }

    //     return view('admin.payroll.uploadSalaryDetails.pagination', ['results' => $results, 'month' => $request->month]);
    // }

    public function import(Request $request)
    {
        $this->validate($request, [
            'select_file' => 'required|mimes:csv,xls,xlsx',
        ]);

        if ($request->file('select_file')) {
            $duplicateId   = [];
            $duplicateName = [];
            $path          = $request->file('select_file')->getRealPath();
            $data          = Excel::load($path)->get();
            $date          = \Carbon\Carbon::now();
            $lastMonth     = $date->subMonth()->format('Y-m');
            foreach ($data as $value) {
                // dd($value);
                $A                     = $value->A;
                $B                     = $value->B;
                $C                     = $value->C;
                $D                     = $value->D;
                $R                     = $value->E;
                $F                     = $value->F;
                $basic_salary          = $value->basic_salary;
                $H                     = $value->H;
                $total_absence_amount  = $value->total_absence_amount;
                $J                     = $value->J;
                $K                     = $value->K;
                $L                     = $value->L;
                $M                     = $value->M;
                $N                     = $value->N;
                $tax                   = $value->tax;
                $total_deduction       = $value->total_deduction;
                $net_salary            = $value->net_salary;
                $total_overtime_amount = $value->total_overtime_amount;
                $gross_salary          = $value->gross_salary;
                $employee_id           = $value->employee_id;

                $duplicateId   = DB::table('salary_details')->where('month_of_salary', $lastMonth)->where('employee_id', $value->employee_id)->first();
                $duplicateName = DB::table('employee')->where('employee_id', $value->employee_id)->select('first_name', 'last_name')->first();

                if (!$duplicateId
                    && isset($basic_salary) && isset($total_absence_amount) && isset($tax) 
                    && isset($total_deduction) && isset($net_salary)
                    && isset($total_overtime_amount)
                     && isset($gross_salary) && isset($employee_id)
                     ) {
                    $salary_detail_list[] = [
                        'basic_salary'          => $basic_salary,
                        'taxable_salary'        => $basic_salary,
                        'total_absence_amount'  => $total_absence_amount,
                        'tax'                   => $tax,
                        'total_deduction'       => $total_deduction,
                        'net_salary'            => $net_salary,
                        'total_overtime_amount' => $total_overtime_amount,
                        'gross_salary'          => $gross_salary,
                        'employee_id'           => $employee_id,
                        // 'total_allowance'       => $value[0],
                         // 'total_late'            => $value[0],
                         // 'total_late_amount'     => $value[0],
                         // 'total_absence'         => $value[0],
                         // 'overtime_rate'         => $value[0],
                         // 'per_day_salary'        => $value[0],
                         // 'total_over_time_hour'  => $value[0],
                         // 'hourly_rate'           => $value[0],
                         // 'total_present'         => $value[0],
                         // 'total_leave'           => $value[0],
                         // 'total_working_days'    => $value[0],
                         // 'working_hour'        => $value[0],
                         'month_of_salary'       => $lastMonth,
                        'created_by'            => 1,
                        'updated_by'            => 1,
                        'status'                => 1,
                        'comment'               => null,
                        'payment_method'        => 'cash',
                        'action'                => 'monthlySalary',
                        'created_at'            => Carbon::now()->subDay(1),
                        'updated_at'            => Carbon::now()];
                } elseif ($duplicateId) {
                    return \back()->with('danger', 'Duplicate entries found for an employee name - ' . $duplicateName->first_name . ' ' . $duplicateName->last_name . ', ' . 'id - ' . $employee_id);
                    // break;
                } else {
                    return back()->with('danger', 'Cell-Heading Not Found in sheet!, Please Check the File');
                }

            }
            // print_r($value);
            // print_r($attendance_list);
            // print_r($fp);
            // print_r($time);
            if (!empty($salary_detail_list)) {
                $date      = \Carbon\Carbon::now();
                $lastMonth = $date->subMonth()->format('y-m');
                try {
                    DB::beginTransaction();
                    DB::table('salary_details')->insert($salary_detail_list);
                    // \Session::flash('success', 'File improted successfully.');
                    DB::commit();
                    return back()->with('success', 'Employee salary information imported successfully.');
                } catch (\Exception $e) {
                    DB::rollback();
                    $e->getMessage();
                    return back()->with('danger', 'Something Went Wrong!, Please try Again.');
                }
            }
        } else {
            return back()->with('danger', 'No Data Found!, Please Check the File');
        }

    }

    public function export(Request $request)
    {
        //     // $start_date   = $month . '-01';
        //     // $end_date     = date("Y-m-t", strtotime($start_date));

        $salaryReport = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS EmployeeName'), 'employee.employee_id as Employee Id', 'month_of_salary as Salary Month',
            'basic_salary as Basic Salary', 'total_allowance as Total Allowance', 'total_deduction as Total Deduction', 'tax as Tax to be Paid', 'gross_salary as Gross Salary', )
            ->join('salary_details', 'salary_details.employee_id', 'employee.employee_id')
            ->where('employee.status', UserStatus::$ACTIVE)
            ->where('salary_details.status', 1)
        // ->where('salary_details.month_of_salary', $request->monthField)
            ->orderBy('salary_details.month_of_salary', 'desc')->get()->toArray();

        $exportFormat = Excel::create('Salary Details', function ($excel) use ($salaryReport) {
            $excel->sheet('Salary Details', function ($sheet) use ($salaryReport) {
                $sheet->fromArray($salaryReport);
            });
        })->download($request->type);

        // $exportFormat = Excel::create('Laravel Excel', function ($excel) use ($salaryReport) {
        //     $excel->sheet('Excel sheet', function ($sheet) use ($salaryReport) {
        //         $sheet->loadView('admin.payroll.uploadSalaryDetails.pagination', $salaryReport);
        //         $sheet->setOrientation('landscape');
        //     });
        // })->export($request->type);

        return $exportFormat;

        // $salaryReportFilter = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS EmployeeName'), 'employee.employee_id as Employee Id', 'employee.finger_id as Finger Id', 'month_of_salary as Salary Month',
        //     'basic_salary as Basic Salary', 'total_allowance as Total Allowance', 'total_deduction as Total Deduction', 'tax as Tax to be Paid', 'gross_salary as Gross Salary', )
        //     ->join('salary_details', 'salary_details.employee_id', 'employee.employee_id')
        //     ->where('employee.status', UserStatus::$ACTIVE)
        //     // ->where('salary_details.month_of_salary', $request->monthField)
        // // ->where('salary_details.month_of_salary', Carbon::now()->subMonth(1)->format('y-m'))
        //     ->get()->toArray();
        // // Carbon::now()->subMonth(2)->format('y-m')

        // $exportFormat = Excel::create('Salary Details', function ($excel) use ($salaryReportFilter) {
        //     $excel->sheet('Salary Details', function ($sheet) use ($salaryReportFilter) {
        //         $sheet->fromArray($salaryReportFilter);
        //     });
        // })->download($request->type);

        // $data = [
        //     'exportFormat' => $exportFormat,
        //     'month'        => $request->monthField,
        // ];
        // return $data;

    }

    public function export1(Request $request)
    {
        $printHead = PrintHeadSetting::first();
        // return $departmentList;
        $results = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS EmployeeName'), 'employee.employee_id as Employee Id', 'month_of_salary as Salary Month',
            'basic_salary as Basic Salary', 'total_allowance as Total Allowance', 'total_deduction as Total Deduction', 'tax as Tax to be Paid', 'gross_salary as Gross Salary', )
            ->join('salary_details', 'salary_details.employee_id', 'employee.employee_id')
            ->where('employee.status', UserStatus::$ACTIVE)->where('salary_details.month_of_salary', $request->monthField)->get()->toArray();

        $data = [
            'results'   => $results,
            'month'     => $request->monthField,
            'printHead' => $printHead,
        ];

        $pdf = PDF::loadView('admin.payroll.uploadSalaryDetails.pdf.monthlySalaryDetailsPdf', $data);
        $pdf->setPaper('A4', 'landscape');
        $pageName = "SalaryDetails " . $request->monthField . ".pdf";
        return $pdf->download($pageName);
    }

    public function downloadFile()
    {
        $file_name = 'templates/employee_salary.xlsx';
        $file      = Storage::disk('public')->get($file_name);
        return (new Response($file, 200))
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    // public function something()
    // {

    //     $something2 = '';
    //     $something  = '';
    //     $results    = Employee::select(DB::raw('CONCAT(COALESCE(employee.first_name,\'\'),\' \',COALESCE(employee.last_name,\'\')) AS EmployeeName'), 'employee.employee_id as Employee Id', 'month_of_salary as Salary Month',
    //         'basic_salary as Basic Salary', 'total_allowance as Total Allowance', 'total_deduction as Total Deduction', 'tax as Tax to be Paid', 'gross_salary as Gross Salary', )
    //         ->join('salary_details', 'salary_details.employee_id', 'employee.employee_id')
    //         ->where('employee.status', UserStatus::$ACTIVE)
    //     // ->where('salary_details.month_of_salary', $request->monthField)
    //         ->get()->toArray();

    //     Excel::create('Laravel Excel', function ($excel) use ($results) {

    //         $excel->sheet('Excel sheet', function ($sheet) use ($results) {
    //             $sheet->loadView('admin.payroll.uploadSalaryDetails.pagination', $results);
    //             // ->with('something', $results)
    //             // ->with('something2', $results);
    //             $sheet->setOrientation('landscape');
    //         });

    //     })->export('xls');

    // }

}
