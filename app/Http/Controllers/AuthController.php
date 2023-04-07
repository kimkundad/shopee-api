<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('phone', 'password');

        $token = auth('api')->attempt($credentials);
     
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = auth('api')->user();

        if($user->isVerified == false) {

            $response = array(
                'success' => false,
                'message' => 'คุณยังไม่ได้ทำการ Verify เบอร์โทรศัพท์'
            );
            return response()->json($response);

        }else{

            return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ], 200);

        }


    }


    public function userProfile() {
        return response()->json(
            [auth('api')->user()], 200
        );
    }

    public function refresh()
    {
        return response()->json([
            'status' => 'success',
            'user' => Auth::user(),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }

    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'numeric', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {

            $message = $validator->errors()->first();
            $errors=$validator->errors();
            $code='200';
            $response = array(
                'success' => false,
                'message' => $message,
                "errors" => $errors
            );
            return response()->json($response);
            }

        $country = 'th';
        $phone   = $request->phone;
        $phone2 = '';
        $phone2 = $this->phonize($phone, $country);
 
        /* Get credentials from .env */
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $twilio->verify->v2->services($twilio_verify_sid)
            ->verifications
            ->create($phone2, "sms");

        User::create([
            'name' => $request->phone,
            'phone' => $request->phone,
            'email' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'msg' => 'success please Verifying Phone number OTP',
            'phone' => $request->phone,
        ], 201);
    }


    public function verify(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'verification_code' => ['required', 'numeric'],
            'phone' => ['required', 'string'],
        ]);

        //$request->verification_code

        if ($validator->fails()) {

            $message = $validator->errors()->first();
            $errors=$validator->errors();
            $code='200';
            $response = array(
                'success' => false,
                'message' => $message,
                "errors" => $errors
            );
            return response()->json($response);
            }

        $country = 'th';
        $phone   = $request->phone;
        $phone2 = '';
        $phone2 = $this->phonize($phone, $country);
        

        /* Get credentials from .env */
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio_sid = getenv("TWILIO_SID");
        $twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $twilio = new Client($twilio_sid, $token);
        $verification = $twilio->verify->v2->services($twilio_verify_sid)
            ->verificationChecks
            ->create(['code' => $request->verification_code, 'to' => $phone2]);
        if ($verification->valid) {
            $user = tap(User::where('phone', $request->phone))->update(['isVerified' => true]);

            $myuser = User::where('phone', $request->phone)->first();
            /* Authenticate user */
         //   Auth::login($user->first());
            $token = Auth::login($myuser);
            return response()->json([
                'status' => 'success',
                'msg' => 'Phone number verified',
                'user' => $myuser,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ],
                'phone' => $request->phone,
            ], 201);
        }

        return response()->json([
            'msg' => 'Invalid verification code entered!',
            'phone' => $request->phone,
        ], 400);

    }


    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }


    public function phonize($phoneNumber, $country) {

        $countryCodes = array(
            'th' => '+66',
            'de' => '+43',
            'it' => '+39'
        );
    
        return preg_replace('/[^0-9+]/', '',
               preg_replace('/^0/', $countryCodes[$country], $phoneNumber));
    }


}
