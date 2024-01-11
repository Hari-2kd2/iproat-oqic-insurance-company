<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Model\MsSql;
use App\Model\Employee;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\AccessLogEvent;
use App\Model\EmployeeInOutData;
use App\Model\EmployeeAttendance;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Lib\Enumerations\AppConstant;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Repositories\ApiAttendanceRepository;

class AttendanceController extends Controller
{

    public function import(Request $request)
    {

        DB::beginTransaction();
        $att = new EmployeeInOutData();
        $att->employee_attendance_id = $request->employee_attendance_id;
        $att->finger_print_id = $request->finger_print_id;
        $att->date = $request->date;
        $att->in_time_from = $request->in_time_from;
        $att->in_time = $request->in_time;
        $att->out_time = $request->out_time;
        $att->out_time_upto = $request->out_time_upto;
        $att->working_time = $request->working_time;
        $att->working_hour = $request->working_hour;
        $att->status = $request->status;
        $att->created_at = $request->created_at;
        $att->updated_at = $request->updated_at;
        $att->in_out_time = $request->in_out_time;
        $att->save();
        DB::commit();

        return response()->json(['status' => 'success', 'message' => 'Attendance imported Successfully updated !'], 200);
    }

    public function reporthistory()
    {
        $localReportID = EmployeeInOutData::orderBy('employee_attendance_id', 'DESC')->first();
        return response()->json(['status' => 'success', 'message' => 'Successfully updated !', 'data' => $localReportID], 200);
    }
    public function loghistory()
    {
        $localLogID = MsSql::where('local_primary_id', '!=', null)->orderBy('primary_id', 'DESC')->first();
        return response()->json(['status' => 'success', 'message' => 'Successfully updated !', 'data' => $localLogID], 200);
    }

    protected $apiAttendanceRepository;

    public function __construct(ApiAttendanceRepository $apiAttendanceRepository)
    {
        $this->apiAttendanceRepository = $apiAttendanceRepository;
    }

    public function sample(Request $request)
    {
        $full_data = date('Y-m-d');
        $date = \explode('-', $full_data);
        return response()->json([
            'message' => "API works fine",
            'date' => $date[2],
        ], 200);
    }

    public function apiattendanceList(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'data' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' =>  $validator->getMessageBag()->first()]);
        }

        $attendanceData = [];

        // live fn
        $array = \json_decode($request->data);

        //local postman
        // $array = \json_encode($request->data);
        // $array = \json_decode($array);

        $count = count($array);
        $bug = null;
        $refresh = false;

        foreach ($array as $key => $value) {

            if ($key == (count($array) - 1) && $value->finger_id != null) {

                $base64_image = $value->face_id;

                $employeeAttendanceDataFormat = $this->apiAttendanceRepository->makeBulkEmployeeAttendacneInformationDataFormat($value);

                // $checkData = MsSql::where('ID', $value->finger_id)->where('datetime', '>=', date('Y-m-d') . ' 00:00:00')->groupBy('type')->get();

                // if (count($checkData) >= 2) {
                //     return response()->json([
                //         'status' => \false,
                //         'refresh' => true,
                //         'message' => 'Your attendance for today has been recorded as closed.',
                //     ], 200);
                // }

                if ($base64_image != 'null') {
                    @list($type, $file_data) = explode(';', $base64_image);
                    @list(, $file_data) = explode(',', $file_data);
                    $imageName = \md5(Str::random(30) . time() . '_' . uniqid()) . '.' . 'jpg';
                    $employeePhoto['face_id'] = $imageName;
                    Storage::disk('faceid')->put($imageName, base64_decode($base64_image));
                }

                if (isset($employeePhoto)) {
                    $employeeData = $employeeAttendanceDataFormat + $employeePhoto;
                } else {
                    $employeeData = $employeeAttendanceDataFormat;
                }

                // $is_checked_in = MsSql::where('ID', $value->finger_id)->where('datetime', '>=', date('Y-m-d') . ' 00:00:00')->orderByDesc('datetime')
                //     ->select('ms_sql.*', 'datetime as in_out_time')->first();

                // info($is_checked_in);
                // info($employeeData['check_type']);

                // if (isset($is_checked_in) && $is_checked_in->type != 'IN') {

                //     info($is_checked_in->type);
                //     info($employeeData['check_type']);

                //     return response()->json([
                //         'status' => false,
                //         'refresh' => true,
                //         'message' => 'You have been checked in using some other devices.',
                //     ], 200);
                // }

                try {

                    DB::beginTransaction();

                    $attendanceData[] = EmployeeAttendance::create($employeeData);

                    $ms_sql_max_local_id = MsSql::max('local_primary_id');

                    $attDataSet = [
                        'ID' => $employeeData['finger_print_id'],
                        'datetime' => $employeeData['in_out_time'],
                        'type' => $employeeData['check_type'],
                        'employee' => $employeeData['employee_id'],
                        'device_name' => 'Mobile',
                        'status' => AppConstant::$OKEY,
                        // 'inout_status' => $employeeData['inout_status'],
                        'local_primary_id' => $ms_sql_max_local_id,
                        'created_at' => Carbon::now(),
                        'updated_at' =>  Carbon::now(),
                    ];

                    MsSql::insert($attDataSet);
                    // event(new AccessLogEvent($attDataSet));

                    DB::commit();
                    $bug = 0;
                } catch (\Exception $e) {
                    DB::rollback();
                    info($e->getMessage());
                    $bug = 1;
                }
            }
        }

        if ($bug == 0) {

            return response()->json([
                'status' => \true,
                'count' => $count,
                'refresh' => $refresh,
                'message' => 'Employee attendance successfully saved.',
            ], 200);
        } else {

            return response()->json([
                'status' => \false,
                'count' => $count,
                'refresh' => $refresh,
                'message' => 'Something Error Found !, Please try again.',
            ], 200);
        }
    }

    public function apiattendanceIn(Request $request)
    {
        Log::info("apiattendance");

        $face_id = $request->file('face_id');

        if ($face_id) {
            $imgName = md5(Str::random(30) . time() . '_' . $request->file('face_id')) . '.' . $request->file('face_id')->getClientOriginalExtension();
            $request->file('face_id')->move('uploads/faceId/', $imgName);
            $employeePhoto['face_id'] = $imgName;
        }

        $status = \false;

        $employeeAtendacneDataFormat = $this->apiAttendanceRepository->makeEmployeeAttendacneInformationDataFormat($status, $request->all());

        if (isset($employeePhoto)) {
            $employeeData = $employeeAtendacneDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeAtendacneDataFormat;
        }

        try {

            DB::beginTransaction();
            $attendanceData = EmployeeAttendance::create($employeeData);
            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {

            return $e;
            DB::rollback();
            $bug = 1;
        }

        if ($bug == 0) {
            return response()->json([
                'status' => \true,
                'message' => 'Employee attendance successfully saved.',
                'data' => $attendanceData,
            ], 200);
        } else {
            return response()->json([
                'status' => \false,
                'message' => 'Something Error Found !, Please try again.',
                'data' => $attendanceData,
            ], 200);
        }
    }

    public function apiattendance(Request $request)
    {

        $base64_image = $request->face_id;

        $uri = Route::getFacadeRoot()->current()->uri();

        if ($base64_image != 'null' && isset($base64_image)) {
            @list($type, $file_data) = explode(';', $base64_image);
            @list(, $file_data) = explode(',', $file_data);
            $imageName = \md5(Str::random(30) . time() . '_' . uniqid()) . '.' . 'jpg';
            $employeePhoto['face_id'] = $imageName;
            Storage::disk('faceid')->put($imageName, base64_decode($base64_image));
        }

        $employeeAtendanceDataFormat = $this->apiAttendanceRepository->makeEmployeeAttendacneInformationDataFormat($uri, $request->all());

        if ($employeeAtendanceDataFormat == false) {
            return response()->json([
                'status' => \false,
                'message' => 'Something Error Found !, Please try again.',
            ], 200);
        }

        if (isset($employeePhoto)) {
            $employeeData = $employeeAtendanceDataFormat + $employeePhoto;
        } else {
            $employeeData = $employeeAtendanceDataFormat;
        }

        try {

            DB::beginTransaction();

            $attendanceData = EmployeeAttendance::updateOrCreate($employeeData);

            $ms_sql = MsSql::create([
                'ID' => $employeeData['finger_print_id'],
                'datetime' => $employeeData['in_out_time'],
                'type' => $employeeData['check_type'],
                'employee' => $employeeData['employee_id'],
                'device_name' => 'Mobile',
                'devuid' => 'Mobile',
                'status' => 0,
            ]);

            DB::commit();
            $bug = 0;
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            $bug = 1;
        }

        if ($bug == 0) {
            return response()->json([
                'status' => \true,
                'message' => 'Employee attendance successfully saved.',
                'data' => $attendanceData,
            ], 200);
        } else {
            return response()->json([
                'status' => \false,
                'message' => 'Something Error Found !, Please try again.',
                'data' => $attendanceData,
            ], 200);
        }
    }

    public function deviceAttendance()
    {
        $employee = Employee::where('user_id', Auth::user()->user_id)->first();

        $is_checked_in = MsSql::where('ID', $employee->finger_id)->where('datetime', '>=', date('Y-m-d') . ' 00:00:00')->orderByDesc('datetime')
            ->select('ms_sql.*', 'datetime as in_out_time')->first();

        info($is_checked_in);

        $dataSet = [
            'is_checked_in' => isset($is_checked_in->type) && $is_checked_in->type == 'IN' ? true : false,
            'checked_in_data' => $is_checked_in,
        ];

        info($is_checked_in);

        return $dataSet;
    }
}
