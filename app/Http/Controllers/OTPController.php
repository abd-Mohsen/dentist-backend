<?php

namespace App\Http\Controllers;

use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Http\Request;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;

class OTPController extends Controller
{
    public function sendRegisterOTP(Request $request) : JsonResponse
    {
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'how tf you got this error'], 400);
        $otp = (new Otp())->generate($user->id, 5, 10);
        $this->sendOTP($user, $otp->token);

        return response()->json([
            'message' => 'OTP sent successfully.',
            "url" => URL::signedRoute('verification.otp', ['id' => $user->id]),
        ]);
    }

    public function verifyRegisterOTP(Request $request) : JsonResponse
    {
        $user = $request->user();
        $result = (new Otp())->validate($user->id, $request->otp);

        if (!$user) return response()->json(['message' => 'you are not authorized'], 401);
        if (!$result->status) return response()->json(false, 400);
        if ($user->hasVerifiedEmail()) return response()->json(['message' => 'Email already verified'], 403);//if so, go to home
        
        $user->markEmailAsVerified();
        event(new Verified($user));

        // logout the user or let him enter home page (and delete token from mobile app)
        return response()->json(true);
    }

    public function sendResetOTP(Request $request) : JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'User not found.'], 400);
        
        $otp = (new Otp())->generate($user->id, 5, 10);
        $this->sendOTP($user, $otp->token);

        return response()->json(['message' => 'Reset OTP sent successfully.'],201);
    }

    public function verifyResetOTP(Request $request) : JsonResponse
    {
        $request->validate([
            "email"=>"required|email",
            "otp"=>"required|string"
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'wrong email'], 400);
        
        $result = (new Otp())->validate($user->id, $request->otp);

        if (!$result->status) return response()->json(false, 400);
            
        $resetToken = Password::createToken($user);
        return response()->json(['reset_token' => $resetToken], 201);
    }

    public function resetPassword(Request $request) : JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'wrong email'], 400);

        $status = Password::reset(
            $request->validate([
                //'email' => 'required|email',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|string|min:8',
                'token' => 'required|string'
            ]),
            function (User $user, string $password) {
                $user->password = bcrypt($password);
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ?  response()->json(true, 200)
            :  response()->json(["message" => "reset has failed"], 500);
    }

    private function sendOTP($user, $otp){
        Mail::to($user->email)->send(new OTPMail($otp));
    }

}