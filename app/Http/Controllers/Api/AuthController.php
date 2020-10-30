<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Models\Profile;
use Lcobucci\JWT\Parser;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Jobs\Api\User\RegisterUser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Notifications\signupActivate;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function signup(RegisterRequest $request)
    {
        
        if ($request->validator->fails()) {
            $errors = $request->validator->messages();
            return response()->json(compact('errors'), 400);
        }

        $user = $this->dispatchNow(RegisterUser::fromRequest($request));

        Profile::create(['user_id' => $user->id]);

        return response()->json([], 200);
    }

    public function requestConfirmationEmail(Request $request)
    {
        $this->validate($request, [
            'email' => ['required', 'string', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->activation_token == '' && $user->active == 1) {
            return response()->json([
                'code' => 'ACCOUNT_ACTIVATED',
                'message' => 'Your account has been activated.',
                'data' => ['user' => $user]
            ], 200);
        }

        try {
            $user->notify(new signupActivate($user));
        } catch (\Swift_TransportException $e) {
            $errors = ['message' => 'Unable to send activation email.'];
            return response()->json(compact('errors'), 422);
        }

        return response()->json([
            'code' => 'EMAIL_SENT',
            'message' => 'Confirmation email sent'
        ], 200);
    }

    public function signupActivate($token)
    {
        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_TOKEN',
                'message' => 'This activation token is invalid.'
            ], 404);
        }

        $user->active = true;
        $user->activation_token = '';
        $user->save();

        return response()->json([
                'success' => true,
                'code' => 'ACCOUNT_ACTIVATED',
                'message' => 'Your account has been activated.',
                'data' => ['user' => $user]
            ], 200);
    }

    public function login(LoginRequest $request)
    {
        if ($request->validator->fails()) {
            $errors = $request->validator->messages();
            return response()->json(compact('errors'), 400);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {
            // if (auth()->user()->active != 1) {
            //     $errors = ['message' => 'You have not confirmed your account.'];
            //     return response()->json(compact('errors'), 403);
            // }

            $token = auth()->user()->createToken('Property Tweet User')->accessToken;
            
            return response()->json([
                'success' => true,
                'code' => 'LOGIN_SUCCESS',
                'message' => 'You have successfully logged in.',
                'data' => [
                    'token' => $token
                ]
            ], 200);
        } else {
            return response([
                'success' => false,
                'code' => 'INVALID_LOGIN',
                'message' => 'Email and password do not match',
                'data' => []
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $value = $request->bearerToken();
        $id = (new Parser())->parse($value)->getHeader('jti');
        $token = $request->user()->tokens->find($id);
        $token->revoke();

        return response([
                'success' => true,
                'code' => 'LOGGED_OUT',
                'message' => 'You have successfully been logged out.',
                'data' => []
        ], 200);
    }

    public function changePassword(Request $request)
    {   
        $validationRules = [
            'old_password' => ['required', 'string', 'min:8'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed']
        ];

        $validator = \Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_FIELD',
                'message' => 'Invalid fields. Check form and try again.',
                'data' => $validator->messages()
            ], 400);
        }

        if (Hash::check($request->old_password, auth()->user()->password)) {
            $user = auth()->user()->update([
                    'password' => Hash::make($request->new_password)
                ]);
        } else {
            return response([
                    'success' => false,
                    'code' => 'PASSWORD_MISMATCH',
                    'message' => 'Your old password does not match our record.',
                    'data' => []
            ], 400);
        }

        return response([
                'success' => true,
                'code' => 'PASSWORD_CHANGED',
                'message' => 'You have successfully changed your password.',
                'data' => ['user' => auth()->user()]
        ], 200);
    }
}
