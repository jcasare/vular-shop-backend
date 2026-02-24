<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $remember = $credentials['remember_me'] ?? false;

        if (!Auth::attempt($credentials, $remember)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->is_admin == 0) {
            Auth::logout();
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json(['access_token' => $token, 'user' => new UserResource($user)]);
    }

    public function register()
    {
        return view('auth.register');
    }

    public function logout()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->currentAccessToken();
        $token->delete();
        return response('', 204);
    }

    public function user(Request $request)
    {
        return new UserResource($request->user());
    }
}
