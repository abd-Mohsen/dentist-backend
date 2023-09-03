<?php

namespace App\Http\Controllers;

use App\Mail\TestMail;
use App\Models\User;
use Illuminate\Http\Request;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;

class OTPController extends Controller
{
    public function sendRegisterOTP(Request $request) : JsonResponse
    {
        //$user = User::where('email', $request->email)->first();
        $user = $request->user();
        if (!$user) return response()->json(['message' => 'how tf you got this error'], 400);
        $otp = (new Otp())->generate($user->id, 5, 10);
        $this->sendOTP($user, 123);

        return response()->json([
            'message' => 'OTP sent successfully.',
            "url" => URL::signedRoute('verification.otp', ['id' => auth()->user()->id]),
        ]);
    }

    public function verifyRegisterOTP(Request $request) : JsonResponse
    {
        //$user = User::where('email', $request->email)->first();
        $user = $request->user();
        $result = (new Otp())->validate($user->id, $request->otp);

        if (!$user) return response()->json(['message' => 'wtf'], 400);
        if (!$result->status) return response()->json(false, 400);
        if ($user->hasVerifiedEmail()) return response()->json(['message' => 'Email already verified'], 400);
        
        $user->markEmailAsVerified();
        event(new Verified($user));

        // logout the user or let him enter home page (and delete token from mobile app)
        return response()->json(true);
    }

    public function sendResetOTP(Request $request) : JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'User not found.'], 404);
        
        $otp = Otp::generate($user->id,5,10);
        $this->sendOTP($user->email, $otp);

        return response()->json(['message' => 'Reset OTP sent successfully.'],201);
    }

    public function verifyResetOTP(Request $request) : JsonResponse
    {
        $request->validate([
            "email"=>"required|email",
            "otp"=>"required"
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'no account?'], 400);
        
        $result = Otp::validate($user->id , $request->otp);

        if (!$result->status) return response()->json(false, 400);
            
        $resetToken = Password::createToken($user);
        return response()->json(['reset_token' => $resetToken], 201);
    }

    public function resetPassword(Request $request) : JsonResponse
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) return response()->json(['message' => 'no account?'], 400);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
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

    private function sendOTP($user, $otp)
    {
        // Mail::raw(
        //     "Your OTP is: $otp",
        //      function ($message) use ($email) {
        //         $message->to($email)->subject('OTP Verification');
        //     }
        // );

        Mail::to($user)->send(new TestMail($otp));

        // Mail::to($email)->send();
        // if(Mail::failures() != 0) return "sent successfully";
        // return "ooops";

        // $mg = Mailgun::create('your-mailgun-api-key'); // Use your Mailgun API key here

        // $mg->messages()->send('your-mailgun-domain', [
        //     'from' => 'your-email@example.com',
        //     'to' => $email,
        //     'subject' => 'OTP Verification',
        //     'text' => "Your OTP is: $otp",
        // ]);
    }

}