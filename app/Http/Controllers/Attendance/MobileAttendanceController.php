<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Model\EmployeeAttendance;
use App\Model\EmployeeInOutData;
use Illuminate\Http\Request;

class MobileAttendanceController extends Controller
{
    public function mobileAttendance(Request $request)
    {

        $attendanceData = [];

        if ($_POST) {
            $attendanceData = EmployeeInOutData::join('employee', 'employee.finger_id', 'view_employee_in_out_data.finger_print_id')
                ->join('department', 'department.department_id', 'employee.department_id')
                ->join('designation', 'designation.designation_id', 'employee.designation_id')
            // ->join('ms_sql', 'ms_sql.ID', 'view_employee_in_out_data.finger_print_id')
                ->where('employee.status', 1)
                ->where('view_employee_in_out_data.date', '>=', dateConvertFormtoDB($request->from_date))
                ->where('view_employee_in_out_data.date', '<=', dateConvertFormtoDB($request->to_date))
                ->whereIn('view_employee_in_out_data.device_name', ['Mobile', 'mobile'])
                ->groupBy('view_employee_in_out_data.finger_print_id', 'view_employee_in_out_data.date')
                ->get();

            return View('admin.attendance.mobile.index', ['attendanceData' => $attendanceData, 'from_date' => $request->from_date, 'to_date' => $request->to_date]);
        }

        return \view('admin.attendance.mobile.index', ['attendanceData' => $attendanceData, 'from_date' => $request->from_date, 'to_date' => $request->to_date]);
    }

    public function mobileAttendanceReport(Request $request)
    {
        $employee_id = $request->employee_id;
        $date = $request->date;

        $employeeInfo = EmployeeAttendance::join('employee', 'employee.employee_id', 'employee_attendance.employee_id')
            ->join('department', 'department.department_id', 'employee.department_id')
            ->join('view_employee_in_out_data', 'view_employee_in_out_data.finger_print_id', 'employee_attendance.finger_print_id')
            ->join('designation', 'designation.designation_id', 'employee.designation_id')
            ->where('employee_attendance.employee_id', $employee_id)
            ->where('employee_attendance.in_out_time', '>=', date('Y-m-d H:i:s', strtotime($date . '05:30:00')))
            ->whereDate('view_employee_in_out_data.date', $date)
            ->where('employee_attendance.in_out_time', '<=', date('Y-m-d H:i:s', strtotime((date('Y-m-d', strtotime($date . " +1 day"))) . '08:00:00')))
            ->groupBy('employee_attendance.employee_attendance_id')
            ->where('employee_attendance.uri', 'api')
            ->select('employee_attendance.*', 'department.department_name', 'designation.designation_name', 'employee.first_name', 'employee.last_name')
            ->orderBy('employee_attendance.in_out_time', 'ASC')
            ->get();

        // dd($employeeInfo, $date);

        $attDataFormat = [];
        $distance = [];
        $location = [];
        $km = [];
        $i = 0;
        $map_dataset = [];

        foreach ($employeeInfo as $key => $value) {
            $i++;

            if ($value->check_type == 'OUT' && $key = 0) {
                $i--;
            }
            if ($value->check_type == 'IN') {
                $attDataFormat[$i]['finger_id'] = $value->finger_print_id;
                $attDataFormat[$i]['first_name'] = $value->first_name;
                $attDataFormat[$i]['employee_id'] = $value->employee_id;
                $attDataFormat[$i]['department_name'] = $value->department_name;
                $attDataFormat[$i]['designation_name'] = $value->designation_name;
                $attDataFormat[$i]['in_time'] = $value->in_out_time;
                $attDataFormat[$i]['date'] = date('Y-m-d', strtotime($value->in_out_time));
                $attDataFormat[$i]['lat_in'] = $value->latitude;
                $attDataFormat[$i]['lng_in'] = $value->longitude;
                $attDataFormat[$i]['face_id_in'] = $value->face_id;
                $attDataFormat[$i]['check_in'] = $value->check_type;
                $location[$i]['lat'] = $value->latitude;
                $location[$i]['lng'] = $value->longitude;
                $in_address = $this->getaddress($value->latitude, $value->longitude);
                $attDataFormat[$i]['in_address'] = $in_address;
                $map_dataset[] = ["timestamp" => $in_address, "latitude" => $value->latitude, "longitude" => $value->longitude];
            }

            if ($value->check_type == 'OUT') {
                $attDataFormat[$i - 1]['face_id_out'] = $value->face_id;
                $attDataFormat[$i - 1]['out_time'] = $value->in_out_time;
                $attDataFormat[$i - 1]['lat_out'] = $value->latitude;
                $attDataFormat[$i - 1]['lng_out'] = $value->longitude;
                $attDataFormat[$i - 1]['check_out'] = $value->check_type;
                $out_address = $this->getaddress($value->latitude, $value->longitude);
                $attDataFormat[$i - 1]['out_address'] = $out_address;
                $map_dataset[] = ["timestamp" => $out_address, "latitude" => $value->latitude, "longitude" => $value->longitude];
            }

            if ($key % 2 == 0 && $key >= 2 && count($attDataFormat) > 0) {
                $attDataFormat[$i]['distance'] = $this->getDistanceOfTwoPoints($attDataFormat[$i - 2]['lat_in'], $attDataFormat[$i - 2]['lng_in'], $attDataFormat[$i]['lat_in'], $attDataFormat[$i]['lng_in']);
            }
        }

        try {
            if (count($attDataFormat) > 2) {
                for ($i = 1; $i < count($location); $i += 2) {
                    $distance = $this->getDistanceOfTwoPoints($location[$i]['lat'], $location[$i]['lng'], $location[$i + 1]['lat'], $location[$i + 1]['lng']);
                    $km[$i] = number_format((float) $distance, 6, '.', '') . ' ' . 'Km';
                }
            }
        } catch (\Throwable $th) {
            $distance = [];
        }

        return view('admin.attendance.mobile.mobileAttendanceReport', ['employeeInfo' => $attDataFormat, 'distance' => $distance, 'employee_id' => $request->employee_id, 'date' => $request->date, 'map_dataset' => json_encode($map_dataset)]);
    }

    public function GetDrivingDistance($lat1, $lat2, $long1, $long2)
    {
        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL&key=AIzaSyAckm5gPM2pV9we3Clrxuyg3WJsv0BE51s";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
        $time = $response_a['rows'][0]['elements'][0]['duration']['text'];

        return array('distance' => $dist, 'time' => $time);
    }

    public function distance($lat1, $lon1, $lat2, $lon2)
    {

        $pi80 = M_PI / 180;
        $lat1 *= $pi80;
        $lon1 *= $pi80;
        $lat2 *= $pi80;
        $lon2 *= $pi80;

        $r = 6372.797; // mean radius of Earth in km
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $km = $r * $c;

        //echo '<br/>'.$km;
        return $km;
    }

    public static function getDistanceOfTwoPoints($lat1, $lon1, $lat2, $lon2, $unit = 'K')
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "M") {
            return ($miles * 1.609344 * 1000);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function getaddress($lat, $lng)
    {
        // $lat = 12.8248356;
        // $lng = 80.2069394;

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&sensor=false&key=AIzaSyAckm5gPM2pV9we3Clrxuyg3WJsv0BE51s';
        $json = file_get_contents($url);
        $data = json_decode($json);
        // dd($data);
        $status = $data->status;
        if ($status == "OK") {
            return $data->results[0]->formatted_address;
        } else {
            return null;
        }
    }

    public function DMStoDEC($deg, $min, $sec)
    {
        // Converts DMS ( Degrees / minutes / seconds )
        // to decimal format longitude / latitude

        return $deg + ((($min * 60) + ($sec)) / 3600);
    }

    public function DECtoDMS($dec)
    {
        // Converts decimal longitude / latitude to DMS
        // ( Degrees / minutes / seconds )

        // This is the piece of code which may appear to
        // be inefficient, but to avoid issues with floating
        // point math we extract the integer part and the float
        // part by using a string function.

        $vars = explode(".", $dec);
        $deg = $vars[0];
        $tempma = "0." . $vars[1];

        $tempma = $tempma * 3600;
        $min = floor($tempma / 60);
        $sec = $tempma - ($min * 60);

        return array("deg" => $deg, "min" => $min, "sec" => $sec);
    }

    public function sample()
    {
        $lng = 80.2069394;
        $result = $this->DECtoDMS($lng);
        return $result['deg'];
    }
}
