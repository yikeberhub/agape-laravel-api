namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;

use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Handle user registration
        if($request->user()->role !='admin'){
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        //create users

        $user = User::create([
            'email': $request->email,
            'password': Hash::make($request->password),
            'role': $request->role,
            'first_name': $request->first_name,
            'middle_name':$request->middle_name,
            'last_name': $request->last_name,
            'gender':$request->gender,
            'phone_number': $request->phone_number,

            ])

        $token = Password.getRepository()->create($user);
        $verificationLink = url('api/auth/email-verify?uid='.Str:encode($user->id).'&token='.$token);

        Mail::send('emails.verify',['link'=>$verificationLink],funcation($message) use($user){
            $message->to($user->email);
            $mesage->subject('Email Verification for agape mobility');
        });

        return response()->json([
            'message'=>'user created successfully please verify your email',
            ],201)
    }

    public function verifyEmail(Request $request)
    {
        // Handle email verification
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