<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\ApproveOvertimeReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveOverTimeRequest;
use App\Imports\ApprovedOvertimeImport;
use App\Lib\Enumerations\AppConstant;
use App\Model\ApproveOverTime;
use App\Model\Branch;
use App\Model\EmployeeInOutData;
use App\Repositories\CommonRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ApproveOverTimeController extends Controller
{

    protected $commonRepository;

    public function __construct(CommonRepository $commonRepository)
    {
        $this->commonRepository = $commonRepository;
    }
    public function index(Request $request)
    {

        $results = [];
        if ($_POST) {
            // dd($request->all());
            $results = ApproveOverTime::where('branch_id', $request->branch_id)->where('date', dateConvertFormtoDB($request->date))
                ->with('branch', 'employee')->get();

            if ($request->department_id != '') {
                $results = ApproveOverTime::where('branch_id', $request->branch_id)->where('date', dateConvertFormtoDB($request->date))
                    ->with('branch', 'employee')->whereHas('employee', function ($q) use ($request) {
                    $q->where('department_id', $request->department_id);
                })->get();
            }
        }

        $departmentList = $this->commonRepository->departmentList();
        $branchList = $this->commonRepository->branchList();

        return view('admin.attendance.approveOvertime.index', ['results' => $results, 'date' => $request->date, 'branch_id' => $request->branch_id,'department_id' => $request->department_id, 'departmentList' => $departmentList, 'branchList' => $branchList]);
    }

    public function create(Request $request)
    {
        $qry = '1 ';
        if ($request->date) {
            $qry = 'date=' . dateConvertFormtoDB($request->date);
        }
        $employeeList = $this->commonRepository->employeeFingerList();

        $employee = EmployeeInOutData::whereRaw($qry)->groupBy('finger_print_id')->get('finger_print_id');
        return view('admin.attendance.approveOvertime.form', ['employeeList' => $employeeList]);
    }

    public function store(ApproveOverTimeRequest $request)
    {
        $input = $request->all();
        $input['date'] = dateConvertFormtoDB($input['date']);
        $input['approved_overtime'] = date("H:i:s", strtotime($_POST['approved_overtime']));
        try {
            $overtime = ApproveOverTime::create($input);
            $employeeInOutData = EmployeeInOutData::where('finger_print_id', $overtime->finger_print_id)->where('date', $overtime->date)->update(['approve_over_time_id' => $overtime->approve_over_time_id]);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect('approveOvertime')->with('success', 'Overtime successfully saved.');
        } else {
            return redirect('approveOvertime')->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function edit($id)
    {
        $employeeList = $this->commonRepository->employeeFingerList();

        $editModeData = ApproveOverTime::findOrFail($id);
        // select(DB::raw('DATE_FORMAT(approve_over_time.date, "%d/%m/%Y") as date'))->where('approve_over_time_id', $id)->first();

        return view('admin.attendance.approveOvertime.form', ['editModeData' => $editModeData, 'employeeList' => $employeeList]);
    }

    public function update(ApproveOverTimeRequest $request, $id)
    {
        $overtime = ApproveOverTime::findOrFail($id);
        $input = $request->all();
        try {
            $overtime->update($input);
            $employeeInOutData = EmployeeInOutData::where('finger_print_id', $overtime->finger_print_id)->where('date', $overtime->date)->update(['approve_over_time_id' => $overtime->approve_over_time_id]);

            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            return redirect()->back()->with('success', 'Overtime successfully updated ');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function destroy($id)
    {

        $count = EmployeeInOutData::where('approve_over_time_id', '=', $id)->count();

        if ($count > 0) {
            return 'hasForeignKey';
        }

        try {
            $overtime = ApproveOverTime::FindOrFail($id);
            $employeeInOutData = EmployeeInOutData::where('finger_print_id', $overtime->finger_print_id)->where('date', $overtime->date)->update(['approve_over_time_id' => null]);

            $overtime->delete();

            $bug = 0;
        } catch (\Exception $e) {
            $bug = 1;
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo 'error';
        }
    }

    public function reportDetails(Request $request)
    {
        $approval = ApproveOverTime::where('finger_print_id', $request->finger_print_id)->where('date', dateConvertFormtoDB($request->date))->exists();
        $reportDetails = EmployeeInOutData::where('finger_print_id', '=', $request->finger_print_id)->where('date', dateConvertFormtoDB($request->date))->first();
        $overtime = isset($reportDetails->over_time) ? $reportDetails->over_time : 'notFound';
        $overtime = $overtime != null ? $overtime : "00:00:00";
        return $overtime;
    }

    // public function approveOvertimeTemplate()
    // {
    //     $file_name = 'templates/approveovertime_detail.xlsx';
    //     $file = Storage::disk('public')->get($file_name);
    //     return (new Response($file, 200))
    //         ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // }

    public function approveOvertimeTemplate(Request $request)
    {
        $date = dateConvertFormtoDB($request->date);
        $inc = 1;
        $dataSet = [];
        $Data = EmployeeInOutData::where('date', $date)->where('over_time', '>=', AppConstant::$MINIMUM_OT_HOUR)->orderBy('finger_print_id', 'ASC')->get();

        foreach ($Data as $key => $data) {

            $dataSet[] = [
                $inc,
                $data->finger_print_id,
                $data->date,
                $data->over_time,
                $data->over_time,
                'Simple Approval',
            ];

            $inc++;
        }

        $primaryHead = ['SL.NO', 'EMPLOYEE ID', 'DATE', 'ACTUAL OT', 'APPROVED OT', 'REMARK'];
        $heading = [$primaryHead];

        $extraData['heading'] = $heading;
        $filename = 'Employee Overtime Information-' . DATE('d-m-Y His') . '.xlsx';

        return Excel::download(new ApproveOvertimeReport($dataSet, $extraData), $filename);
    }

    public function export(Request $request)
    {
        $date = dateConvertFormtoDB($request->date);
        $Data = ApproveOverTime::where('date', $date)->orderBy('approve_over_time_id', 'ASC')->get();
        $inc = 1;
        foreach ($Data as $key => $data) {
            if (isset($data->branch_id)) {
                $branch = Branch::find($data->branch_id);
                $branch_name = $branch->branch_name;
            }

            // if (isset($data->finger_print_id)) {
            //     $employee = Employee::where('finger_id', $data->finger_print_id)->first();
            //     $employee_name = $employee->first_name . ' ' . $employee->last_name;
            // }

            $dataSet[] = [
                $inc,
                $data->finger_print_id,
                $data->date,
                $data->actual_overtime,
                $data->approved_overtime,
                $data->remark,
            ];
            $inc++;
        }

        $primaryHead = ['Sl.No', 'Branch', 'EmployeeID', 'Date', 'Actual OT', 'Approval OT', 'Remark', 'Status'];
        $heading = [$primaryHead];

        $extraData['heading'] = $heading;
        $filename = 'Employee Overtime Information-' . DATE('d-m-Y His') . '.xlsx';

        return Excel::download(new ApproveOvertimeReport($dataSet, $extraData), $filename);
    }

    public function import(Request $request)
    {
        try {

            $date = dateConvertFormtoDB($request->date);
            $file = $request->file('select_file');
            Excel::import(new ApprovedOvertimeImport($request->all()), $file);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $import = new ApprovedOvertimeImport();
            $import->import($file);

            foreach ($import->failures() as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }
        return back()->with('success', 'Approve Overtime information imported successfully.');
    }
}
