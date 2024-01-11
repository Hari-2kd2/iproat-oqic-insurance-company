<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Model\SalaryDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Redirect;

class RazorpayController extends Controller
{
    public function razorpay()
    {
        return view('admin.payroll.salarysheet.payment');
    }

    public function payment(Request $request)
    {
        //Input items of form
        $input = Input::all();
        //get API Configuration
        $api = new Api("rzp_test_EzVAI0XNlc8bPq", "bxvBaGVNPKuCj4qmJuiTJyoK");
        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));

                $data['status']         = 1;
                $data['comment']        = $request->comment;
                $data['payment_method'] = "RazorPay";
                $data['created_by']     = Auth::user()->user_id;
                $data['updated_by']     = Auth::user()->user_id;
                $data['created_at']     = Carbon::now();
                $data['updated_at']     = Carbon::now();

                if ($response) {
                    $store = SalaryDetails::where('salary_details_id', $request->salary_details_id)->update($data);
                }
            } catch (\Exception $e) {
                return $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back();
            }

            // Do something here for store payment details in database...
        }

        Session::put('success', 'Payment successful, your order will be despatched in the next 48 hours.');
        return redirect()->back();
    }

    public function makePayment(Request $request)
    {

        try {

        } catch (\Exception $e) {
            $bug = $e->errorInfo[1];
        }

        if ($bug == 0) {
            echo "success";
        } else {
            echo "error";
        }
    }
}
