<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Google2FA;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Symfony\Component\HttpFoundation\Response;
use \ParagonIE\ConstantTime\Base32;

class Google2FAController extends Controller
{
    use ValidatesRequests;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('web');
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function enableTwoFactor(Request $request)
    {
        //generate new secret
        $secret = $this->generateSecret();

        //get user
        // $user = auth()->user();
        $user = $request->user();

        //encrypt and then save secret
        $user->google2fa_secret = Crypt::encrypt($secret);
        $user->save();

        //generate image for QR barcode
        $imageDataUri = \Google2FA::getQRCodeInline(
            $request->getHttpHost(),
            $user->email,
            $secret,
            200
        );
        return view('2fa/enableTwoFactor', ['image' => $imageDataUri,
            'secret' => $secret]);
    }

    /**
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function disableTwoFactor(Request $request)
    {

        // $validator = Validator::make($request->all(), [
        //     'password' => 'required',
        //     'password_confirmation' => 'required|same:password',

        // ]);

        // if ($validator->fails()) {
        //     // echo 'validation_error';
        //     $responseArr['message'] = $validator->errors();
        //     $responseArr['token'] = '';
        //     return response()->json($responseArr, Response::HTTP_BAD_REQUEST);
        // }

        // return $validator;

        $user = $request->user();
        $auth = User::where('user_id', $user->user_id)->where('password', $request->password)->exists();

        if ($auth) {
            //make secret column blank
            $user->google2fa_secret = null;
            $user->save();
            echo 'success';
            // return view('2fa/disableTwoFactor');
        }

        echo 'error';

    }

    /**
     * Generate a secret key in Base32 format
     *
     * @return string
     */
    private function generateSecret()
    {
        $randomBytes = random_bytes(10);
        return Base32::encodeUpper($randomBytes);
    }
}
