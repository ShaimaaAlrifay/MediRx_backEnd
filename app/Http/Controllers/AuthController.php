<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function signIn(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'NID' => ['required'],
                'phone_number' => ['required', 'string'],
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $user = User::where('NID', $request->input('NID'))
                        ->where('phone_number', $request->input('phone_number'))
                        ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized.',
                ], 401);
            }

            Auth::login($user);

            return response()->json([
                'status' => true,
                'message' => 'User signed in successfully',
                'token' => $user->createToken('API TOKEN')->plainTextToken,
                'user_id' => $user->id,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function signOut()
    {
        Auth::logout();
        return response()->json([
            'status' => true,
            'message' => 'Successfully signed out',
        ]);
    }
}

