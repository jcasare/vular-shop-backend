<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerLoginRequest;
use App\Http\Requests\CustomerRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class CustomerAuthController extends Controller
{
    public function register(CustomerRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'customer',
                'is_admin' => false,
            ]);

            Customer::create([
                'user_id' => $user->id,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'status' => 'active',
            ]);

            return $user;
        });

        $token = $user->createToken('customer_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => new UserResource($user->load('customer')),
        ], 201);
    }

    public function login(CustomerLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->role !== 'customer') {
            Auth::logout();
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        $token = $user->createToken('customer_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => new UserResource($user->load('customer')),
        ]);
    }

    public function googleRedirect(): JsonResponse
    {
        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');
        $url = $driver->stateless()
            ->redirect()
            ->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    public function googleCallback(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        /** @var \Laravel\Socialite\Two\GoogleProvider $driver */
        $driver = Socialite::driver('google');
        $googleUser = $driver->stateless()->user();

        $user = DB::transaction(function () use ($googleUser) {
            // Check if user already linked via google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                $user->update([
                    'avatar' => $googleUser->getAvatar(),
                ]);
                return $user;
            }

            // Check if a user with this email already exists (registered via email/password)
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                return $user;
            }

            // Create new user + customer profile
            $nameParts = explode(' ', $googleUser->getName(), 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => 'customer',
                'is_admin' => false,
            ]);

            Customer::create([
                'user_id' => $user->id,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'status' => 'active',
            ]);

            return $user;
        });

        $token = $user->createToken('customer_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => new UserResource($user->load('customer')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $user->currentAccessToken();
        $token->delete();

        return response()->json([], 204);
    }

    public function user(Request $request): JsonResponse
    {
        $user = $request->user()->load('customer');

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }
}
