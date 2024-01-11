<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Model\AccessControl;
use App\Model\Device;
use App\Model\Employee;
use App\User;
use Auth;
use Illuminate\Http\Request;
use App\Components\Common;

class DeviceController extends Controller
{

    //protected $employeeRepositories;

    /* public function __construct(EmployeeRepository $employeeRepositories){
    $this->employeeRepositories = $employeeRepositories;
    }
     */

    public function importemployee(Request $request)
    {

        try {


            $check_device=Common::restartdevice();
            $check_device=json_decode($check_device);
            if($check_device->status=="all_offline_check_cable"){
                return redirect()->back()->with('error',$check_device->msg);
            }


            \DB::beginTransaction();

            $emp_data = [];

            $device = Device::where('id', $request->device)->where('status', 1)->first();

            if ($device->device_status != 'online') {
                return redirect()->back()->with('error', 'Device currently offline!, Please try again.');
            }

            $rawdata = ["UserInfoSearchCond" => [
                "searchID"             => (string) rand(23, 9999),
                "searchResultPosition" => 0,
                "maxResults"           => 3000,
            ],
            ];

            // dd(\json_encode($rawdata));

            $client   = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'http://localhost:' . $device->port . '/' . $device->protocol . '/AccessControl/UserInfo/Search', [
                'auth'  => [$device->username, $device->password, "digest"],
                'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                'json'  => $rawdata,
            ]);

            $statusCode = $response->getStatusCode();
            $content    = $response->getBody()->getContents();
            $data       = json_decode($content);
            if($data->UserInfoSearch->numOfMatches==0){
                return redirect()->back()->with('success', 'Employee Records empty !');
            }


            // dd($data->UserInfoSearch->UserInfo);
            //{"UserInfoOutList":{"UserInfoOut":[{"employeeNo":"","errorCode": ,"errorMsg": "","statusCode": ,"statusString":"","subStatusCode": ""},{"employeeNo":"","errorCode": ,"errorMsg": "","statusCode": ,"statusString":"","subStatusCode": ""}]}

            $emp_json = $data->UserInfoSearch->UserInfo;
            foreach ($emp_json as $key => $Data) {
                $emp         = [];
                $check_exist = Employee::whereRaw('device_employee_id="' . $Data->employeeNo . '" OR finger_id="' . $Data->employeeNo . '"')->first();
                if (!$check_exist) {
                    $emp['role_id']            = 3;
                    $emp['user_name']          = $employee_no          = $Data->name;
                    $emp['device_employee_id'] = $employee_no = $Data->employeeNo;
                    $emp['status']             = 1;
                    $emp['created_by']         = Auth::user()->user_id;
                    $emp['updated_by']         = Auth::user()->user_id;
                    $parentData                = User::create($emp);
                    $emp_data[]                = ['user_id' => $parentData->user_id, 'employee_no' => $Data->employeeNo, 'name' => $Data->name];
                } else {
                    $emp_data[] = ['user_id' => $check_exist->user_id, 'employee_no' => $Data->employeeNo, 'name' => $Data->name];
                }
            }

            if (count($emp_data)) {
                $i = 0;
                foreach ($emp_data as $Data) {
                    $i++;
                    $rawdata = [
                        "FaceInfoSearchCond" => [
                            "searchID"             => (string) rand(23, 9999),
                            "searchResultPosition" => 0,
                            "maxResults"           => 100,
                            "employeeNo"           => (string) $Data['employee_no'],
                            "faceLibType"          => "blackFD",
                        ],
                    ];

                    $client   = new \GuzzleHttp\Client();
                    $response = $client->request('POST', 'http://localhost:' . $device->port . '/' . $device->protocol . '/Intelligent/FDLib/FDSearch', [
                        'auth'  => [$device->username, $device->password, "digest"],
                        'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                        'json'  => $rawdata,
                    ]);

                    $statusCode = $response->getStatusCode();
                    $content    = $response->getBody()->getContents();
                    $data       = json_decode($content);
                    $face_data  = $data->FaceInfoSearch->FaceInfo[0];

                    $imgName = "";

                    if (isset($face_data->faceURL) && $face_data->faceURL) {
                        $face_path = explode("/HikGatewayStorage", $face_data->faceURL);
                        $face_path = $face_path[1];

                        $client   = new \GuzzleHttp\Client();
                        $response = $client->request('GET', $face_path, [
                            'headers' => ['Accept-Encoding' => 'gzip, deflate, br'],
                            'auth'    => [$device->username, $device->password, "digest"],
                            // 'query' => ['format' => 'json', 'devIndex' => $device->devIndex],
                        ]);

                        $imgName  = $i . rand(1, 10000) . date('Y_m_d_H_i_s') . '.png';
                        $pic_path = 'uploads/employeePhoto/' . $imgName;
                        file_put_contents($pic_path, $response->getBody()->getContents());
                    }

                    $check_employee = Employee::whereRaw('device_employee_id="' . $Data['employee_no'] . '" OR finger_id="' . $Data['employee_no'] . '"')->first();
                    if (!$check_employee) {
                        $employeeData['first_name']         = $Data['name'];
                        $employeeData['user_id']            = $Data['user_id'];
                        $employeeData['photo']              = $imgName;
                        $employeeData['device_employee_id'] = $Data['employee_no'];
                        $employeeData['finger_id']          = $Data['employee_no'];
                        $employee_qry                       = Employee::create($employeeData);
                        $employeeID                         = $employee_qry->employee_id;
                    } else {
                        if ($imgName) {
                            $check_employee->photo = $imgName;
                            $check_employee->save();
                        }
                        $employeeID                         = $check_employee->employee_id;
                        $check_employee->device_employee_id = $Data['employee_no'];
                        $check_employee->save();

                    }

                    $access_chk = AccessControl::where('device_employee_id', $Data['employee_no'])->where('device', $request->device)->first();
                    if (!$access_chk) {
                        $acc_ins                     = new AccessControl;
                        $acc_ins->employee           = $employeeID;
                        $acc_ins->device             = $device->id;
                        $acc_ins->status             = 1;
                        $acc_ins->device_employee_id = $Data['employee_no'];
                        $acc_ins->save();
                    }

                }
            }
            \DB::commit();
            return redirect()->back()->with('success', 'Employee Details imported successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error',Common::errormsg());
            // dd($e);
        }

    }

    public function testlog(Request $request)
    {
        \Log::info($request);
        return \response()->json([
            'message' => 'success',
        ], 200);
    }

}
