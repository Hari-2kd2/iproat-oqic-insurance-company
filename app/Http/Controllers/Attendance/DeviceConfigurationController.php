<?php

namespace App\Http\Controllers\Attendance;

use App\Components\Common;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeviceConfigRequest;
use App\Model\AccessControl;
use App\Model\Device;
use App\Model\Employee;
use App\Model\MsSql;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeviceConfigurationController extends Controller
{

    public function index()
    {
        $results = Device::where('status', 1)->get();
        return view('admin.attendance.device.index', ['results' => $results]);
    }

    public function create()
    {

        $userList = User::where('role_id', 1)->get();
        return view('admin.attendance.device.form', ['userList' => $userList]);
    }

    public function store(DeviceConfigRequest $request)
    {
        $input = $request->all();

        try {

            $rawdata = [
                "DeviceInList" => [
                    [
                        "Device" => [
                            "protocolType" => $request->protocol,
                            "devName" => $request->name,
                            "devType" => "AccessControl",
                            "ISAPIParams" => [
                                "addressingFormatType" => "IPV4Address",
                                "address" => $request->ip,
                                "portNo" => (int) $request->port,
                                "userName" => $request->username,
                                "password" => $request->password,
                            ],
                        ],
                    ],
                ],
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'http://localhost:' . $request->port . '/' . $request->protocol . '/ContentMgmt/DeviceMgmt/addDevice?format=json', [
                'auth' => [$request->username, $request->password, "digest"],
                'json' => $rawdata,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();
            $json = json_decode($content);

            $input['devIndex'] = $json->DeviceOutList[0]->Device->devIndex;
            $input['devResponse'] = $content;

            $rawdata = [
                "SearchDescription" => [
                    "position" => 0,
                    "maxResult" => 100,
                    "Filter" => [
                        "key" => $request->ip,
                        "devType" => "AccessControl",
                        "protocolType" => ["ISAPI"],
                        "devStatus" => ["online", "offline"],
                    ],
                ],
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'http://localhost:' . $request->port . '/' . $request->protocol . '/ContentMgmt/DeviceMgmt/deviceList?format=json', [
                'auth' => [$request->username, $request->password, "digest"],
                'json' => $rawdata,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();
            $data = json_decode($content);
            $deviceInfo = $data->SearchResult->MatchList[0]->Device;
            $input['model'] = $deviceInfo->devMode;
            $input['device_status'] = $deviceInfo->devStatus;

            if ($input['device_status'] == "online") {
                $input['verification_status'] = 1;
            }

            $device = Device::create($input);

            //Push to LIVE

            $form_data = $request->all();
            $form_data['id'] = $device->id;
            $form_data['model'] = $device->model;
            $form_data['device_status'] = $device->device_status;
            $form_data['verification_status'] = $device->verification_status;
            unset($form_data['_method']);
            unset($form_data['_token']);
            $data_set = [];
            foreach ($form_data as $key => $value) {
                if ($value) {
                    $data_set[$key] = $value;
                } else {
                    $data_set[$key] = '';
                }

            }

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', Common::liveurl() . "addDevice", [
                'form_params' => $data_set,
            ]);

            // PUSH TO LIVE END

            $bug = 0;
        } catch (\Exception $e) {
            // dd($e);
            $bug = 1;
        }

        //print_r($bug);

        /*$client = new \GuzzleHttp\Client();
        $response = $client->request('GET','https://jsonplaceholder.typicode.com/todos/1', [
        'query' => [
        'key1' => 1,
        ],
        ]);

        $statusCode = $response->getStatusCode();
        $content = $response->getBody()->getContents();
        dd($content);*/

        if ($bug == 0) {
            return redirect('deviceConfigure')->with('success', 'Devices successfully saved.');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function edit($id)
    {
        $userList = User::where('role_id', 1)->get();
        $editModeData = Device::findOrFail($id);
        return view('admin.attendance.device.form', ['editModeData' => $editModeData, 'userList' => $userList]);
    }

    public function update(DeviceConfigRequest $request, $id)
    {
        $devices = Device::findOrFail($id);
        $input = $request->all();
        try {

            $rawdata = [
                "DeviceInfo" => [
                    'devIndex' => $devices->devIndex,
                    "protocolType" => $request->protocol,
                    "devName" => $request->name,
                    "devType" => "AccessControl",
                    "ISAPIParams" => [
                        "addressingFormatType" => "IPV4Address",
                        "address" => $request->ip,
                        "portNo" => (int) $request->port,
                        "userName" => $request->username,
                        "password" => $request->password,

                    ],
                ],
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->request('PUT', 'http://localhost:' . $request->port . '/' . $request->protocol . '/ContentMgmt/DeviceMgmt/modDevice?format=json', [
                'auth' => [$request->username, $request->password, "digest"],
                'json' => $rawdata,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();
            $json = json_decode($content);

            if ($json->statusCode != 1 || $json->statusString != "OK") {
                return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
            }

            $rawdata = [
                "SearchDescription" => [
                    "position" => 0,
                    "maxResult" => 100,
                    "Filter" => [
                        "key" => $request->ip,
                        "devType" => "AccessControl",
                        "protocolType" => ["ISAPI"],
                        "devStatus" => ["online", "offline"],
                    ],
                ],
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'http://localhost:' . $request->port . '/' . $request->protocol . '/ContentMgmt/DeviceMgmt/deviceList?format=json', [
                'auth' => [$request->username, $request->password, "digest"],
                'json' => $rawdata,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();
            $data = json_decode($content);
            $deviceInfo = $data->SearchResult->MatchList[0]->Device;
            $input['model'] = $deviceInfo->devMode;
            $input['device_status'] = $deviceInfo->devStatus;

            $devices->update($input);

            //Push to LIVE

            $form_data = $request->all();
            $form_data['id'] = $devices->id;
            $form_data['model'] = $devices->model;
            $form_data['device_status'] = $devices->device_status;
            unset($form_data['_method']);
            unset($form_data['_token']);
            $data_set = [];
            foreach ($form_data as $key => $value) {
                if ($value) {
                    $data_set[$key] = $value;
                } else {
                    $data_set[$key] = '';
                }

            }

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', Common::liveurl() . "editDevice", [
                'form_params' => $data_set,
            ]);

            // PUSH TO LIVE END

            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e;
            //dd($e);
            $bug = 1;
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }

        if ($bug == 0) {
            return redirect('deviceConfigure')->with('success', 'Devices successfully updated.');
        } else {
            return redirect()->back()->with('error', 'Something Error Found !, Please try again.');
        }
    }

    public function refresh()
    {

        $check_device = Common::restartdevice();
        $check_device = json_decode($check_device);
        if ($check_device->status == "all_offline_check_cable") {
            return redirect()->back()->with('error', $check_device->msg);
        } elseif (isset($check_device->offline_device) && $check_device->offline_device) {
            return redirect()->back()->with('error', $check_device->msg);
        } else {
            return redirect()->back()->with('success', 'Devices status refreshed successfully ! ');
        }
    }

    public function destroy($id)
    {

        try {

            DB::beginTransaction();

            $check_device = Common::restartdevice();
            $check_device = json_decode($check_device);
            if ($check_device->status == "all_offline_check_cable") {
                echo "all_device_offline";
                exit;
            }

            $devices = Device::FindOrFail($id);

            if ($devices->device_status != 'online') {
                echo "error";
                exit;
            }

            $access_qry = AccessControl::where('device', $id)->get();

            $remove = [];
            $unsel_emp = [];
            foreach ($access_qry as $remove_emp) {
                $remove[] = ['employeeNo' => (string) $remove_emp->device_employee_id];
                $unsel_emp[] = $remove_emp->device_employee_id;
            }

            if (count($remove)) {

                $rawdata = [
                    "UserInfoDetail" => [
                        "mode" => "byEmployeeNo",
                        "EmployeeNoList" =>
                        $remove,

                    ],
                ];

                //dd(json_encode($rawdata));

                $client = new \GuzzleHttp\Client();
                $response = $client->request('PUT', 'http://localhost:' . $devices->port . '/' . $devices->protocol . '/AccessControl/UserInfoDetail/Delete', [
                    'auth' => [$devices->username, $devices->password, "digest"],
                    'query' => ['format' => 'json', 'devIndex' => $devices->devIndex],
                    'json' => $rawdata,
                ]);

                $statusCode = $response->getStatusCode();
                $content = $response->getBody()->getContents();
                $data = json_decode($content);

                //dd($data);

                $rawdata = [
                    "FaceInfoDelCond" => [
                        "EmployeeNoList" =>
                        $remove,
                    ],
                ];

                //dd(json_encode($rawdata));

                $client = new \GuzzleHttp\Client();
                $response = $client->request('PUT', 'http://localhost:' . $devices->port . '/' . $devices->protocol . '/Intelligent/FDLib/FDSearch/Delete', [
                    'auth' => [$devices->username, $devices->password, "digest"],
                    'query' => ['format' => 'json', 'devIndex' => $devices->devIndex],
                    'json' => $rawdata,
                ]);

                AccessControl::whereIn('device_employee_id', $unsel_emp)->where('device', $id)->delete();
            }

            $rawdata = [
                "DevIndexList" => [
                    $devices->devIndex,
                ],
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', 'http://localhost:' . $devices->port . '/' . $devices->protocol . '/ContentMgmt/DeviceMgmt/delDevice?format=json', [
                'auth' => [$devices->username, $devices->password, "digest"],
                'json' => $rawdata,
            ]);

            $statusCode = $response->getStatusCode();
            $content = $response->getBody()->getContents();
            $data = json_decode($content);

            $devices->status = 2;
            $devices->save();
            $bug = 0;

            //Push to LIVE

            $form_data = [];
            $form_data['id'] = $id;
            unset($form_data['_method']);
            unset($form_data['_token']);

            $data_set = [];
            foreach ($form_data as $key => $value) {
                if ($value) {
                    $data_set[$key] = $value;
                } else {
                    $data_set[$key] = '';
                }

            }

            $client = new \GuzzleHttp\Client();
            $response = $client->request('POST', Common::liveurl() . "deleteDevice", [
                'form_params' => $data_set,
            ]);

            // PUSH TO LIVE END

            \DB::commit();
        } catch (\Exception $e) {
            $bug = 1;
            dd($e);
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo 'error';
        }
    }

    public function logs(Request $request)
    {
        $path = "accesslogs/" . date('YMd');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $myfile = fopen($path . "/log.txt", "a+") or die("Unable to open file!");
        $txt = print_r($_REQUEST, 1);
        fwrite($myfile, $txt);
        $txt = "OUT PUNCH." . DATE('d-m-Y h:i:s A') . ".\n";
        fwrite($myfile, $txt);
        //exit;
        //return true;

        $eventLog = $_REQUEST['event_log'];
        $eventLog = json_decode($eventLog);
        if (isset($eventLog->AccessControllerEvent->employeeNoString)) { //&& isset($Data->attendanceStatus)
            $txt = "EMPSS ID." . $eventLog->AccessControllerEvent->employeeNoString . ".\n";
            fwrite($myfile, $txt);

            $device = Device::where('name', $eventLog->AccessControllerEvent->deviceName)->first();

            /*$txt = "DEvice.".print_r($device,1).".\n";
            fwrite($myfile, $txt);*/

            $time = $eventLog->dateTime;

            $txt = "Time" . DATE('Y-m-d H:i:s', strtotime($time)) . ".\n";
            fwrite($myfile, $txt);

            $log = MsSql::where('device_employee_id', $eventLog->AccessControllerEvent->employeeNoString)->where('device', $device->id)->where('datetime', DATE('Y-m-d H:i:s', strtotime($time)))->first();
            $last_record = MsSql::where('device_employee_id', $eventLog->AccessControllerEvent->employeeNoString)->orderBy('datetime', 'Desc')->first();
            $employee_data = Employee::where('finger_id', $eventLog->AccessControllerEvent->employeeNoString)->first();

            /* $txt = "emp.".print_r($employee_data,1).".\n";
            fwrite($myfile, $txt);*/

            if (!$log && $employee_data) {
                $log_insert = new MsSql;
                $log_insert->ID = $employee_data->finger_id;
                $log_insert->employee = $employee_data->employee_id;
                $log_insert->device = $device->id;
                $log_insert->device_name = $eventLog->AccessControllerEvent->deviceName;
                $log_insert->device_employee_id = $eventLog->AccessControllerEvent->employeeNoString;
                $log_insert->status = 0;

                if (isset($last_record)) {
                    $last_datetime = new \Datetime($last_record->datetime);
                    $current_log_datetime = new \Datetime(DATE('Y-m-d H:i:s', strtotime($time)));
                    $diff = $last_datetime->diff($current_log_datetime);
                }
                $txt = "End\n";
                fwrite($myfile, $txt);

                if ($last_record && (date('Y-m-d', strtotime($last_record->datetime)) != date('Y-m-d', strtotime($time)))) {
                    $log_insert->type = 'IN';
                } elseif ($last_record && $last_record->type == 'IN') {
                    $log_insert->type = 'OUT';
                } else {
                    $log_insert->type = 'IN';
                }

                $log_insert->datetime = DATE('Y-m-d H:i:s', strtotime($time));
                $log_insert->save();
            }
        }

        fclose($myfile);
        return \response()->json([
            'message' => 'success',
        ], 200);
    }
}
