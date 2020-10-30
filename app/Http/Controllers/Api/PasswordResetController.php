<?php

namespace App\Http\Controllers\Api;

use Hash;
use App\User;
use Carbon\Carbon;
use App\PasswordReset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;

class PasswordResetController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email']
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_EMAIL',
                'message' => 'We can\'t find a user with that email address.',
                'data' => []
            ], 404);
        }

        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email], 
            [
                'email' => $user->email,
                'token' => str_random(60)
            ]
        );

        if ($user && $passwordReset) {
            $user->notify(new PasswordResetRequest($passwordReset->token));

            return response()->json([
                    'success' => true,
                    'code' => 'EMAIL_SENT',
                    'message' => 'We have sent you an email containing your password reset link!',
                    'data' => []
                ]);
        }
    }

    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_TOKEN',
                'message' => 'This password reset token is invalid.',
                'data' => []
            ], 404);
        }

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'success' => false,
                'code' => 'INVALID_TOKEN',
                'message' => 'This password reset token is invalid.',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'code' => 'TOKEN_FOUND',
            'message' => 'Reset token found.',
            'data' => []
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed'],
            'token' => ['required', 'string']
        ]);

        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();

        if (!$passwordReset) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_TOKEN',
                'message' => 'This password reset token is invalid.',
                'data' => []
            ], 404);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_EMAIL',
                'message' => 'We can\'t find a user with that email address.',
                'data' => []
            ], 401);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $passwordReset->delete();

        $user->notify(new PasswordResetSuccess($passwordReset));

        return response()->json([
            'success' => true,
            'code' => 'PASSWORD_CHANGED',
            'message' => 'Your password has been changed successfully.',
            'data' => [
                'user' => $user
            ]
        ], 200);
    }
}
