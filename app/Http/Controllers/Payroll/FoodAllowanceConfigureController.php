<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Model\FoodAllowanceDeductionRule;
use Illuminate\Http\Request;

class FoodAllowanceConfigureController extends Controller
{

    public function index()
    {
        $data = FoodAllowanceDeductionRule::first();
        return view('admin.payroll.foodDeductionConfigure.foodDeductionConfigure', ['data' => $data]);
    }

    public function updateFoodDeductionConfigure(Request $request)
    {
        $input = $request->all();
        $data  = FoodAllowanceDeductionRule::findOrFail($request->food_allowance_deduction_rule_id);

        try {
            $data->update($input);
            // $data = FoodAllowanceDeductionRule::where('food_allowance_deduction_rule_id', $request->food_allowance_deduction_rule_id)->update($input);
            $bug  = 0;
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
