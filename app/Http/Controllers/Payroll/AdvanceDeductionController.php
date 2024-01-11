<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdvanceDeductionRequest;
use App\Model\AdvanceDeduction;
use App\Model\Employee;
use App\Repositories\PayrollRepository;
use DateTime;
use Illuminate\Http\Request;

class AdvanceDeductionController extends Controller
{

    protected $_attendanceRepository;

    public function __construct(PayrollRepository $payrollRepository)
    {
        $this->payrollRepository = $payrollRepository;
    }

    public function index()
    {
        $results = Employee::join('advance_deduction', 'advance_deduction.employee_id', '=', 'employee.employee_id')->select('advance_deduction.*', 'employee.first_name', 'employee.last_name')->get();
        // $results      = AdvanceDeduction::get();
        return view('admin.payroll.advanceDeduction.index', ['results' => $results]);
    }

    public function create()
    {
        $results   = [];
        $employees = Employee::where('status', 1)->get();
        foreach ($employees as $employee) {
            $results[$employee->employee_id][] = $employee;
        }
        return view('admin.payroll.advanceDeduction.form', ['results' => $results]);
    }

    public function store(AdvanceDeductionRequest $request)
    {
        $input = $this->payrollRepository->makeEmployeeAdvanceDetuctionDataFormat($request->all());
        try {
            AdvanceDeduction::create([
                'employee_id'                => $request->employee_id,
                'advance_amount'             => $request->advance_amount,
                'date_of_advance_given'      => dateConvertFormtoDB($request->date_of_advance_given),
                'deduction_amouth_per_month' => $request->deduction_amouth_per_month,
                'no_of_month_to_be_deducted' => $request->no_of_month_to_be_deducted,
            ]);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return redirect('advanceDeduction')->with('success', 'Advance deduction Successfully saved.');
        } else {
            return redirect('advanceDeduction')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function edit($id)
    {

        $results   = [];
        $employees = Employee::where('status', 1)->get();
        foreach ($employees as $employee) {
            $results[$employee->employee_id][] = $employee;
        }
        $editModeData = AdvanceDeduction::findOrFail($id);
        // return $editModeData;
        return view('admin.payroll.advanceDeduction.form', ['editModeData' => $editModeData, 'results' => $results]);
    }

    public function update(Request $request, $id)
    {
        $data = AdvanceDeduction::FindOrFail($id);
        // $input = $this->payrollRepository->makeEmployeeAdvanceDetuctionDataFormat($request->all());
        $input = $request->all();
        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Advance deduction Successfully Updated.');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $data = AdvanceDeduction::FindOrFail($id);
            $data->delete();
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            echo "success";
        } elseif ($bug == 1451) {
            echo 'hasForeignKey';
        } else {
            echo 'error';
        }
    }

    public function calculateEmployeeAdvanceDeduction(Request $request)
    {
        // $results           = [];
        // $advanceDeductions = AdvanceDeduction::join('employee', 'employee.employee_id', '=', 'advance_deduction.employee_id')->where('advance_deduction.employee_id', $request->employee_id)->select('advance_deduction.*')->get();
        // foreach ($advanceDeductions as $value) {
        //     $results[$value->employee_id] = $advanceDeductions;
        // }
        // return $results;

        $advanceDeductions = AdvanceDeduction::join('employee', 'employee.employee_id', '=', 'advance_deduction.employee_id')
            ->where('advance_deduction.employee_id', $request->employee_id)->select('advance_deduction.*', 'advance_deduction.created_at')->get();
        //    return $advanceDeductions;
        $deductionArray = [];
        $totalDeduction = 0;
        foreach ($advanceDeductions as $key => $deduction) {
            $temp                               = [];
            $temp['advance_deduction_id']       = $deduction->advance_deduction_id;
            $temp['employee_id']                = $deduction->employee_id;
            $temp['advance_amount']             = $deduction->advance_amount;
            $temp['date_of_advance_given']      = $deduction->date_of_advance_given;
            $temp['deduction_amouth_per_month'] = $deduction->deduction_amouth_per_month;
            $temp['no_of_month_to_be_deducted'] = $deduction->no_of_month_to_be_deducted;
            $temp['status']                     = $deduction->status;

            $temp['date']             = $deduction->date_of_advance_given;
            $temp['format_date']      = new DateTime($temp['date']);
            $temp['advanced_year']   = $temp['format_date']->format('y');
            $temp['advanced_month']   = $temp['format_date']->format('m');
            $temp['current_year']    = \Carbon\Carbon::today('y')->format('y');
            $temp['current_month']    = \Carbon\Carbon::today('m')->format('m');
            $temp['total_period']     = $deduction->no_of_month_to_be_deducted + $temp['advanced_month'];
            $temp['remaining_period'] = $temp['total_period'] - $temp['current_month'];

            if ($temp['remaining_period'] > 0) {
                $temp['amount_of_advance_deduction'] = $deduction->deduction_amouth_per_month;
            } else {
                $temp['amount_of_advance_deduction'] = 0;
            }
            $totalDeduction += $temp['amount_of_advance_deduction'];
            $deductionArray[$key] = $temp;
        }
        return ['deductionArray' => $deductionArray, 'totalDeduction' => $totalDeduction];

    }
}
