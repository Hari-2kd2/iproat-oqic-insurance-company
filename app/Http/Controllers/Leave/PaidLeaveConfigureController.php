<?php

namespace App\Http\Controllers\Leave;

use App\Model\PaidLeaveRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\LeaveType;

class PaidLeaveConfigureController extends Controller
{

    public function index()
    {
        $data = PaidLeaveRule::first();
        return view('admin.leave.setup.paidLeaveConfigure', ['data' => $data]);
    }

    public function updatePaidLeaveConfigure(Request $request)
    {
        $input = $request->all();
        $data  = PaidLeaveRule::findOrFail($request->paid_leave_rule_id);
        

        try {
            $data->update($input);
            $bug = 0;
        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            return "success";
        } else {
            return "error";
        }
    }

}
