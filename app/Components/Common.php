<?php

namespace App\Components;

use Exception;
use App\Model\Device;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Common
{

    public static function restartdevice($try_count = 0)
    {

        $device = Device::where('status', 1)->get();

        foreach ($device as $key => $Data) {

            $Data->device_status = 'offline';
            $Data->save();

            try {
                $rawdata = [
                    "SearchDescription" => [
                        "position"  => 0,
                        "maxResult" => 100,
                        "Filter"    => [
                            "key"          => $Data->ip,
                            "devType"      => "AccessControl",
                            "protocolType" => ["ISAPI"],
                            "devStatus"    => ["online", "offline"],
                        ],
                    ],
                ];

                $client   = new \GuzzleHttp\Client();
                $response = $client->request('POST', 'http://localhost:' . $Data->port . '/' . $Data->protocol . '/ContentMgmt/DeviceMgmt/deviceList?format=json', [
                    'auth' => [$Data->username, $Data->password, "digest"],
                    'json' => $rawdata,
                ]);

                $statusCode = $response->getStatusCode();
                $content    = $response->getBody()->getContents();
                $data       = json_decode($content);
                //dd($data);
                if ($data->SearchResult->numOfMatches == 1) {
                    $deviceInfo          = $data->SearchResult->MatchList[0]->Device;
                    $Data->model         = $deviceInfo->devMode;
                    $Data->device_status = $deviceInfo->devStatus;

                    if ($Data->verification_status == 0 && $Data->device_status == "online") {
                        $Data->verification_status = 1;
                    }

                    $Data->save();
                }
            } catch (\Exception $e) {
                //return redirect()->back()->with('error', 'Something went wrong try again ! ');
            }
        }

        $offline_device = Device::where('device_status', 'offline')->where('status', '!=', 2)->get();

        //dd(count($offline_device) , count($device));

        if (count($offline_device) == count($device)) {
            if ($try_count == 0) {
                $out = exec('C:\Program Files\AC Gateway\Guard\stop.bat', $output, $return);
                $out = exec('C:\Program Files\AC Gateway\Guard\start.bat', $output, $return);
                if ($return == 0) {
                    sleep(20);
                    return Common::restartdevice($try_count + 1);
                } else {
                    return Common::restartdevice($try_count + 1);
                }
            } elseif ($try_count < 6) {
                return Common::restartdevice($try_count + 1);
            } elseif ($try_count >= 6) {
                return json_encode(["status" => "all_offline_check_cable", 'msg' => 'All the devices are offline. Please check the network connection !']);
            }
        } else {
            $online_device = Device::where('device_status', 'online')->where('status', '!=', 2)->count();
            if ($online_device != count($device)) {
                //\Log::info($try_count);
                if ($try_count < 6) {
                    sleep(7);
                    return Common::restartdevice($try_count + 1);
                } else {
                    if (count($offline_device)) {
                        $offline_set = [];
                        foreach ($offline_device as $offlineData) {
                            $offline_set[] = $offlineData->name . " ( " . $offlineData->model . " )";
                        }
                        $offlineDevice = implode(", ", $offline_set);
                        return json_encode(["status" => "some_offline", "offline_device" => $offlineDevice, 'msg' => 'The following device(s) are not reachable / offline , so unable to sync. Please check the device connection.The offline Devices are : [ ' . $offlineDevice . ' ]']);
                    } else {
                        return json_encode(["status" => "all_online"]);
                    }
                }
            } else {

                if (count($offline_device)) {
                    $offline_set = [];
                    foreach ($offline_device as $offlineData) {
                        $offline_set[] = $offlineData->name . " ( " . $offlineData->model . " )";
                    }
                    $offlineDevice = implode(", ", $offline_set);
                    return json_encode(["status" => "some_offline", "offline_device" => $offlineDevice, 'msg' => 'The following device(s) are not reachable / offline , so unable to sync. Please check the device connection.The offline Devices are : [ ' . $offlineDevice . ' ]']);
                } else {
                    return json_encode(["status" => "all_online"]);
                }
            }
        }
    }

    public static function clearinternalerror()
    {
        $out = exec('C:\Program Files\AC Gateway\Guard\stop.bat', $output, $return);
        $out = exec('C:\Program Files\AC Gateway\Guard\start.bat', $output, $return);
        sleep(15);
        return true;
    }


    public static function triggerException()
    {
        // using throw keyword
        throw new Exception('Client error:"POSThttp://localhost/ISAP/AccesCantrel/AcsEventformat-json&deyindex=69006054-1770-447-8569-5608A735076 resulted in a `403 Forbidden` response: {"errorCode":805306388."errorMsg":"Internal error.","statusCode":3,"statusString":"Device Error"');
    }

    public static function errormsg()
    {
        return "Device not responding. Please navigate to Device Configuration and click Refresh device service button.";
    }


    public static function liveurl()
    {
        // tata smartfood
        // return "https://ipro-people.com/tatasmartfoodz/api/";
        return "https://localhost/tatasmartfoodz/api/";
    }


    public static function mail($template, $to, $subject, $data)
    {
        $mail = Mail::send($template, $data, function ($message) use ($to, $subject) {
            $message->from('ebulientcatcoc01@gmail.com', 'Bafna Pharmacy');
            $message->to($to, 'Bafna Pharmacy')->subject($subject);
        });
    }

    public static function backup()
    {
        /*
        Needed in SQL File:

        SET GLOBAL sql_mode = '';
        SET SESSION sql_mode = '';
         */
        try {
            $get_all_table_query = "SHOW TABLES";
            $result = DB::select(DB::raw($get_all_table_query));

            $tables = [
                'password_resets',
                'permission_role',
                'permission_user',
                'permissions',
                'personal_access_tokens',
                'role_user',
                'roles',
                'users',
                'migrations',
            ];

            $structure = '';
            $data = '';

            foreach ($tables as $table) {

                $show_table_query = "SHOW CREATE TABLE " . $table . "";

                $show_table_result = DB::select(DB::raw($show_table_query));

                foreach ($show_table_result as $show_table_row) {
                    $show_table_row = (array) $show_table_row;
                    $structure .= "\n\n" . $show_table_row["Create Table"] . ";\n\n";
                }

                $select_query = "SELECT * FROM " . $table;
                $records = DB::select(DB::raw($select_query));

                foreach ($records as $record) {

                    $record = (array) $record;
                    $table_column_array = array_keys($record);

                    foreach ($table_column_array as $key => $name) {
                        $table_column_array[$key] = '`' . $table_column_array[$key] . '`';
                    }

                    $table_value_array = array_values($record);
                    $data .= "\nINSERT INTO $table (";

                    $data .= "" . implode(", ", $table_column_array) . ") VALUES \n";

                    foreach ($table_value_array as $key => $record_column) {
                        $table_value_array[$key] = addslashes($record_column);
                    }

                    $data .= "('" . implode("','", $table_value_array) . "');\n";
                }
            }

            $file_name = public_path() . '/backup/database_backup_on_' . date('y_m_d') . '.sql';
            $file_handle = fopen($file_name, 'w + ');

            $output = $structure . $data;
            fwrite($file_handle, $output);
            fclose($file_handle);

            return redirect()->route('bill')->with('message', 'DB Backup Successfully');
        } catch (\Throwable $th) {
            return redirect()->route('bill')->with('error', $th->getMessage());
        }
    }
}
