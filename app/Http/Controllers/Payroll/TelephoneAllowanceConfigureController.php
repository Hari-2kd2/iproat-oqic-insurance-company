<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Model\TelephoneAllowanceDeductionRule;
use Illuminate\Http\Request;

class TelephoneAllowanceConfigureController extends Controller
{

    public function index()
    {
        $data = TelephoneAllowanceDeductionRule::first();
        return view('admin.payroll.telephoneDeductionConfigure.telephoneDeductionConfigure', ['data' => $data]);
    }

    public function updateTelephoneDeductionConfigure(Request $request)
    {
        $input = $request->all();
        // $data  = TelephoneAllowanceDeductionRule::findOrFail($request->telephone_allowance_deduction_rule_id);

        try {
            TelephoneAllowanceDeductionRule::where('telephone_allowance_deduction_rule_id', $request->telephone_allowance_deduction_rule_id)->update([
                'cost_per_call'   => $request->cost_per_call,
                'limit_per_month' => $request->limit_per_month,
                'status'          => $request->status,
                'remarks'         => $request->remarks,
            ]);
            // $data->update($input);
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

// TelephoneAllowanceDeductionRule::where('telephone_allowance_deduction_rule_id', $request->telephone_allowance_deduction_rule_id)->update([
//     'cost_per_call'   => $request->cost_per_call,
//     'limit_per_month' => $request->limit_per_month,
//     'status'          => $request->status,
//     'remarks'         => $request->remarks,
// ]);
