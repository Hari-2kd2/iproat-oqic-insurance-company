<?php

namespace App\Components;

use Carbon\Carbon;
use App\Model\AttendanceLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;

class CamAttendance
{

    public function createNewToken()
    {
        $secureKey = Config::get('services.camAttendance.secureKey');
        $currentTimeMilliseconds = Config::get('services.camAttendance.currentTimeMilliseconds');
        $accountID = Config::get('services.camAttendance.accountID');

        $encryptedToken = base64_encode($secureKey . "_" . $currentTimeMilliseconds . "_" . $accountID);

        //  $decoded = base64_decode($encryptedToken);
        //  $length = strlen($encryptedToken);

        return $encryptedToken;
    }

    public function staticToken()
    {
        $encryptedToken = Config::get('services.camAttendance.secureKey');

        return $encryptedToken;
    }

    public static function getLogs($fromdate, $todate, $key, $dataType)
    {

        $lastEvaluatedKey = $key;
        $staticToken = Config::get('services.camAttendance.staticToken');
        info([$fromdate, $todate, $lastEvaluatedKey]);

        $client = new \GuzzleHttp\Client();
        dump($key);
        if ($lastEvaluatedKey == null) {
            $response = $client->request('GET', 'https://integration-apis.camattendance.com/prod/public-api/transactions?fromDate=' . $fromdate . '&toDate=' . $todate, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'token' => $staticToken,
                ]
            ]);
        } else {
            $response = $client->request('GET', 'https://integration-apis.camattendance.com/prod/public-api/transactions?fromDate=' . $fromdate . '&toDate=' . $todate . '&lastEvaluatedKey=' . $lastEvaluatedKey, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-type' => 'application/json',
                    'token' => $staticToken,
                ]
            ]);
        }

        if ($dataType != 'json') {
            return $response;
        }

        $code = $response->getStatusCode();
        $body = json_decode($response->getBody()->getContents(), true);

        if (isset($body['response'])) {
            return response()->json([
                'code' => $code,
                'message' => $body['status']['responseStatus'],
                'size' => $body['response']['size'],
                'data' => $body['response']['list'],
            ]);
        } else {
            return response()->json([
                'code' => $code,
                'message' => $body['status']['responseStatus'],
            ]);
        }
    }

    public function getAttendanceLogs($fromdate = null, $dataType = null, $routine = false)
    {
        $lastEvaluatedKey = null;

        $log = AttendanceLog::orderByDesc('attendance_log_id')->first();

        if ($log) {
            $lastEvaluatedKey = $log->lastEvaluatedKey;
            $fromdate = date('Y-m-d', strtotime('-1 days', strtotime($log->date)));
        }

        if ($fromdate == null || $routine == true) {
            $lastEvaluatedKey = null;
            $fromdate = date('Y-m-d');
        }

        $todate = date('Y-m-d', strtotime('+1 days', strtotime($fromdate)));

        info(['Current Start Date' => $fromdate, 'Current End Date' => $todate]);

        $response = CamAttendance::getLogs($fromdate, $todate, $lastEvaluatedKey, $dataType);

        $body = json_decode($response->getBody()->getContents());

        $message = isset($body->status->responseStatus) ? $body->status->responseStatus : 'Something Went Wrong!';

        if (isset($body->response->list)) {

            foreach ($body->response->list as $key => $value) {

                $exists = AttendanceLog::where('employeeId', $value->employeeId)->where('date', $value->date)
                    ->where('time', $value->time)->where('locationName', $value->locationName)->where('locationId', $value->locationId)->where('type', $value->type)->first();

                $value->lastEvaluatedKey = isset($body->response->lastEvaluatedKey) ? $body->response->lastEvaluatedKey : null;
                $value->size = $body->response->size;
                $value->created_at = Carbon::now();
                $value->updated_at = Carbon::now();

                if (!$exists) {
                    dump($value);
                    AttendanceLog::insert((array) $value);
                }
            }
        }

        if ($routine == false) {
            if (!isset($body->response->lastEvaluatedKey)) {
                info($message);
                return ['status' => true, 'nextLoop' => false, 'message' => $message];
            } else {
                info('Next Loop');
                info($message);
                return ['status' => true, 'nextLoop' => true, 'message' => $message];
            }
        } else {
            if (date('Y-m-d', strtotime('-3 days') < $fromdate)) {
                info('Due to routine Loop Stopped.');
                return ['status' => true, 'nextLoop' => false, 'message' => 'Due to routine Loop Stopped.'];
            } else {
                if (!isset($body->response->lastEvaluatedKey)) {
                    info($message);
                    return ['status' => true, 'nextLoop' => false, 'message' => $message];
                } else {
                    info('Next Loop');
                    info($message);
                    return ['status' => true, 'nextLoop' => true, 'message' => $message];
                }
            }
        }
    }

    public function init()
    {
        $init = CamAttendance::getAttendanceLogs();

        if ($init['nextLoop'] == true) {
            return  $this->init();
        } else {
            return json_encode($init);
        }
    }

    public static function multipleDayLog()
    {
        set_time_limit(0);

        $month = findFromDateToDateToAllDate(date('Y-m-01'), date('Y-m-t'));

        foreach ($month as $key => $value) {
            info($value);
            CamAttendance::getAttendanceLogs(date('Y-m-d 00:00:00', strtotime($value['date'])));
        }
    }

    public static function getEmployeeLists()
    {
        set_time_limit(0);

        $secureKey = Config::get('services.camAttendance.secureKey');
        $currentTimeMilliseconds = Config::get('services.camAttendance.currentTimeMilliseconds');
        $accountID = Config::get('services.camAttendance.accountID');

        $encryptedToken = base64_encode($secureKey . "_" . (int) $currentTimeMilliseconds . "_" . $accountID);

        $client = new \GuzzleHttp\Client();
        info($encryptedToken);

        // $res = $client->request('GET', 'https://integration-apis.camattendance.com/prod/public-api/employee', [
        //     'headers' => [
        //         'Accept' => 'application/json',
        //         'Content-type' => 'application/json',
        //         'token' => $encryptedToken,
        //     ]]);

        // return $res;
    }
}
