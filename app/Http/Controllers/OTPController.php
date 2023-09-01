<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

class OTPController extends Controller
{
    public function sendReisterOTP(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) return response()->json(['message' => 'User not found.'], 404);
        
        $otp = rand(10000, 99999);
        $user->otp = $otp;
        $user->save();

        $this->sendOTP($user->email, $otp);

        $accessToken = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message' => 'OTP sent successfully.',
            'access_token' => $accessToken,
        ]);
    }

    public function verifyOTP(Request $request)
    {
        //$user = User::where('email', $request->email)->first();
        $user = $request->user;

        if (!$user) return response()->json(['message' => 'wtf'], 400);
        if ($user->otp !== $request->otp) return response()->json(false, 400);
        if ($user->hasVerifiedEmail()) return response()->json(['message' => 'Email already verified'], 200);
        
        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(true);
    }

    public function sendResetOTP(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) return response()->json(['message' => 'User not found.'], 404);
        
        $otp = $this->rand(10000, 99999);
        $user->reset_otp = $otp;
        $user->reset_otp_created_at = now();
        $user->save();

        $this->sendOTP($user->email, $otp);

        return response()->json(['message' => 'Reset OTP sent successfully.']);
    }

    public function verifyResetOTP(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) return response()->json(['message' => 'no account?'], 400);
        
        if ($user->reset_otp !== $request->otp)
         return response()->json(['message' => 'Invalid OTP.'], 400);
        
        if ($this->isResetOTPExpired($user)) 
         return response()->json(['message' => 'OTP expired.'], 400);
        

        // Generate reset token
        $resetToken = $this->generateResetToken($user);

        return response()->json(['reset_token' => $resetToken]);
    }

    public function resetPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) return response()->json(['message' => 'no account?'], 400);

        if ($user->reset_token !== $request->reset_token) 
         return response()->json(['message' => 'Invalid reset token.'], 401);
        
        if ($this->isResetTokenExpired($user))
         return response()->json(['message' => 'Reset token expired.'], 400);
        
        $user->password = bcrypt($request->password);
        $user->reset_token = null;
        $user->save();

        return response()->json(true, 200);
    }

    private function sendOTP($email, $otp)
    {
        Mail::raw("Your OTP is: $otp", function ($message) use ($email) {
            $message->to($email)->subject('OTP Verification');
        });
    }

    private function isResetOTPExpired(User $user)
    {
        $expirationTime = $user->reset_otp_created_at->addMinutes(15);
        return $expirationTime->isPast();
    }

    private function generateResetToken(User $user)
    {
        // Implement your logic to generate the reset token
    }


    private function isResetTokenExpired(User $user)
    {
        // Implement your logic to check if the reset token has expired
    }
}