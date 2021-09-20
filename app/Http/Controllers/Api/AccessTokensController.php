<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class AccessTokensController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
            'device_name' => 'required',
            'abilities' => 'nullable'
        ]);

        $user = User::where('email', $request->username)
            ->orWhere('mobile', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return Response::json([
                'message' => 'Invalid username or password'
            ], 401);
        }
        $abilities = $request->input('abilities', ['*']);
        if ($abilities && is_string($abilities)) {
            $abilities = explode(',', $abilities);
        }
        $token = $user->createToken($request->device_name, $abilities);
        return Response::json([
            'token' => $token->plainTextToken,
            'user' => $user
        ]);
    }

    public function destroy()
    {
        $user = Auth::guard('sanctum')->user();
        // Revoke all users tokens
        // $user->tokens()->delete();

        // Revoke current user token
        $user->currentAccessToken()->delete();
    }
}
