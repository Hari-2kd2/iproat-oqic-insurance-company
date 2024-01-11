<?php

namespace App\Http\Controllers;

use App\Components\CamAttendance;
use App\Events\AccessLogEvent;
use App\Model\Employee;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($message, $data)
    {
        return response()->json([
            'status' => \true,
            'message' => $message,
            'data' => $data,
        ], 200);
    }

    public function successdualdata($message, $data, $list)
    {
        return response()->json([
            'status' => \true,
            'message' => $message,
            'data' => $data,
            'list' => $list,
        ], 200);
    }

    public function error()
    {
        return response()->json([
            'status' => \false,
            'message' => "Something error found !, Please try again.",
        ], 200);
    }

    public function custom_error($custom_message)
    {
        return response()->json([
            'status' => \false,
            'message' => $custom_message,
        ], 200);
    }
   
}
