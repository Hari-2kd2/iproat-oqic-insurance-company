<?php

namespace App\Http\Controllers\User;

use App\Model\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Lib\Enumerations\UserStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ValidateSecretRequest;

class LoginController extends Controller
{

    public function index()
    {
        if (Auth::check()) {
            return redirect()->intended(url('/dashboard'));
        }

        return view('admin.login');
    }

    public function Auth(LoginRequest $request)
    {

        if (Auth::attempt(['user_name' => $request->user_name, 'password' => $request->user_password])) {
            $userStatus = Auth::user()->status;
            if ($userStatus == UserStatus::$ACTIVE) {

                $employee = Employee::where('user_id', Auth::user()->user_id)->first();
                $user_data = [
                    "user_id" => (Auth::user()->user_id),
                    "user_name" => (Auth::user()->user_name),
                    "role_id" => (Auth::user()->role_id),
                    "employee_id" => ($employee->employee_id),
                    "department_id" => ($employee->department_id),
                    "finger_id" => ($employee->finger_id),
                    "branch_id" => ($employee->branch_id),
                    "email" => ($employee->email),
                ];
                session()->put('logged_session_data', $user_data);
                // return $this->authenticated($request);
                return redirect()->intended(url('/dashboard'));
            } elseif ($userStatus == UserStatus::$INACTIVE) {
                Auth::logout();
                return redirect(url('login'))->withInput()->with('error', 'You are temporary blocked. please contact to admin');
            } else {
                Auth::logout();
                return redirect(url('login'))->withInput()->with('error', 'You are terminated. please contact to admin');
            }
        } else {
            return redirect(url('login'))->withInput()->with('error', 'User name or password does not matched');
        }
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect(url('login'))->with('success', 'logout successful ..!');
    }

    public function authenticated()
    {

        $user = Auth::user();
        if ($user->google2fa_secret) {
            Auth::logout();
            session()->put('2fa:user:id', $user->user_id);
            return redirect('2fa/validate');
        }

        return redirect()->intended('login');
    }

    public function postValidateToken(Request $request)
    {
        //get user id and create cache key
        $userId = $request->session()->pull('2fa:user:id');
        $key = $userId . ':' . $request->totp;

        //use cache to store token to blacklist
        Cache::add($key, true, 4);

        //login and redirect user
        Auth::loginUsingId($userId);

        return redirect()->intended('dashboard');
    }

    public function getValidateToken()
    {
        if (session('2fa:user:id')) {
            return view('2fa/validate');
        }
        return redirect('login');
    }
}
