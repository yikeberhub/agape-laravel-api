<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;
use App\Mail\EmailVerificationMailable;

use App\Http\Requests\RegisterRequest;
use Exception;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Handle user registration
        if ($request->user() && $request->user()->role != 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Create user
        try{
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
            ]);
            

        $token = Password::getRepository()->create($user);
        $encodedId = base64_encode($user->id);
        $app_url = env('APP_URL');
        $verificationLink = $app_url . '/api/auth/email-verify?uid=' . $encodedId . '&token=' . $token;            
        Mail::to($user->email)->send(new EmailVerificationMailable($verificationLink));
        return response()->json(['success'=>true,
        'message' => 'User created successfully, please verify your email'], 
        201);


    }catch(QueryException $e){
        return response()->json(['success'=>false,
        'message'=>'Database error'.$e->getMessage()], 400);
    }
    catch(Exception $e){
        return response()->json(['success'=>false,
        'message' => 'An error occurred: ' . $e->getMessage(),],
        500);
    }

}

    public function verifyEmail(Request $request)
    {
        // Handle email verification
        $request->validate([
            'uid'=>'required|string',
            'token'=>'required|string',

        ]);

        $userId = $request->input('uid');
        $token = $request->input('token');

        $user = User::find($userId);
        if(!$user){
            return response()->json(['success'=>true,
            'message'=>'user not found',
        ],404);
        }
    }

    public function login(Request $request)
    {
        // Handle user login
    }

    public function refreshToken(Request $request)
    {
        // Handle token refresh
    }

    public function resetPassword(Request $request)
    {
        // Handle password reset
    }

    public function verifyOTP(Request $request)
    {
        // Handle OTP verification
    }

    public function setNewPassword(Request $request)
    {
        // Handle setting a new password
    }

    public function logout(Request $request)
    {
        // Handle user logout
    }

    public function currentUserProfile()
    {
        // Return current user's profile
    }
}

