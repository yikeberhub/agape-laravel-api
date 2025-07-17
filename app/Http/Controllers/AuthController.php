<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Mail\EmailVerificationMailable;
use App\Mail\ResetOtpMailable;
use App\Models\UserOtp;


use App\Http\Requests\RegisterRequest;
use Exception;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request) // Use RegisterRequest here
    {
        if ($request->user() && $request->user()->role != 'admin') {
            return jsonResponse(false, 'Unauthorized', null, 403);
        }

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
                'verification_token' => $verificationToken,
            ]);

            $encodedId = base64_encode($user->id);
            $app_url = config('app.url');
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

    $userId = base64_decode($request->query('uid'));
    $token = $request->query('token');

    $user = User::find($userId);

    if (!$user) {
        return response()->view('auth.verify.failure', ['message' => 'User not found'], 404);
    }

    if ($user->email_verified_at) {
        return response()->view('auth.verify.failure', ['message' => 'Email already verified'], 400);
    }

    if ($token !== $user->verification_token) {
        return response()->view('auth.verify.failure', ['message' => 'Invalid or expired token'], 400);
    }

    $user->email_verified_at = now();
    $user->verification_token = null;
    $user->save();

    return view('auth.verify.success');
}

    

public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();
        
        $token = $user->createToken('authToken')->plainTextToken;

        return jsonResponse(true, 'User logged in successfully', [
            'token' => $token,
            'user' => $user,
        ]);
    } else {
        return jsonResponse(false, 'Invalid login credentials', null, 401);
    }
}

    public function refreshToken(Request $request)
    {
        // Handle token refresh
    }

    public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'nullable|email',
        'phone_number' => 'nullable|string',
    ]);

    $user = null;
    if($request->email){
        $user = User::where('email', $request->email)->first();
    } elseif ($request->phone_number) {
        $user = User::where('phone_number', $request->phone_number)->first();
    }

    if (!$user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    $otp = rand(100000, 999999);
    
    UserOtp::create([
        'user_id' => $user->id,
        'otp' => $otp,
        'expires_at' => Carbon::now()->addMinutes(5),
    ]);

    // Send OTP via email (or SMS)
    Mail::to($user->email)->send(new ResetOtpMailable($otp, $user->first_name));
    $type = $request->phone_number ? 'phone number' : 'email';

    return jsonResponse(true, 'OTP sent to your '.$type, [
        'user_id' => $user->id,
    ], 200);
}

public function verifyOTP(Request $request)
{
    $request->validate([
        'user_id' => 'required|integer',
        'otp' => 'required|numeric',
    ]);

    $userOtp = UserOtp::where('user_id', $request->user_id)
                      ->where('otp', $request->otp)
                      ->where('expires_at', '>', Carbon::now())
                      ->latest()
                      ->first();

    if (!$userOtp) {
        return jsonResponse(false, 'Invalid or expired OTP', null, 400);
    }

    $userOtp->delete();

    return jsonResponse(true, 'OTP verified successfully', null, 200);
}

public function setNewPassword(Request $request)
{
    $request->validate([
        'password' => 'required|string|min:8',
        'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return jsonResponse(false, 'User not found', null, 404);
    }

    $user->password = bcrypt($request->password);
    $user->save();

    return jsonResponse(true, 'Password updated successfully', null, 200);
}


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return jsonResponse(true,'successfully logged out!',null,200);
    }

    public function currentUserProfile(Request $request)
    {
        return jsonResponse(true,'user profile',['user'=>$request->user()],200);
    }
}

