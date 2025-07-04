<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
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
            return jsonResponse(false, 'Unauthorized', null, 403);
        }
    
        // Create user
        try {
            $verificationToken = Str::random(60);
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'verification_token'=>$verificationToken,
            ]);
    
            $token = Password::getRepository()->create($user);
            $encodedId = base64_encode($user->id);
            $app_url = env('APP_URL');
            $verificationLink = $app_url . '/api/auth/email-verify?uid=' . $encodedId . '&token=' . $verificationToken;
    
            Mail::to($user->email)->send(new EmailVerificationMailable($verificationLink));
    
            return jsonResponse(true, 'User created successfully, please verify your email', [
                'id' => $user->id,
                'username' => $request->email
            ], 201);
    
        } catch (QueryException $e) {
            return jsonResponse(false, 'Database error: ' . $e->getMessage(), null, 400);
        } catch (Exception $e) {
            return jsonResponse(false, 'An error occurred: ' . $e->getMessage(), null, 500);
        }
    }

    public function verifyEmail(Request $request)
{
    $request->validate([
        'uid' => 'required|string',
        'token' => 'required|string',
    ]);

    $userId = base64_decode($request->input('uid'));
    $token = $request->input('token');

    $user = User::find($userId);
    if (!$user) {
        return jsonResponse(false, 'User not found', null, 404);
    }

    if ($user->email_verified_at) {
        return jsonResponse(false, 'Email already verified', null, 400);
    }

    if ($token !== $user->verification_token) {
        return jsonResponse(false, 'Invalid token', null, 400);
    }

    $user->email_verified_at = now();
    $user->verification_token = null; 
    $user->save();

    return jsonResponse(true, 'Email verified successfully', [
        'user_id' => $user->id,
    ]);
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

