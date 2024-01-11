<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateSecretRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function authenticated(Request $request)
    {

        $user = Auth::user();

        if ($user->google2fa_secret) {
            Auth::logout();

            $request->session()->put('2fa:user:id', $user->user_id);

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
