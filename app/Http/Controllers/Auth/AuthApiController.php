<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\ApiErrorCode;
use App\Exceptions\ApiException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    /**
     * Register a new user.
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            throw new ApiException(ApiErrorCode::ErrValidation, 'Validation failed', 422, $validator->errors()->getMessages());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->json(['data' => 'User created successfully'], 201);
    }

    /**
     * Sign in with password.
     */
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['data' => ['token' => $token, 'user' => $user]]);
        } catch (ValidationException $e) {
            if ($e->getMessage() == 'These credentials do not match our records.') {
                throw new ApiException(ApiErrorCode::ErrUnauthorized, 'Invalid credentials', 401);
            }

            throw $e;
        }
    }

    /**
     * Sign out current user.
     */
    public function destroy(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            return response()->json(['data' => 'User logged out successfully']);
        } catch (\Throwable $e) {
            if ($e->getMessage() == 'Call to a member function tokens() on null') {
                throw new ApiException(ApiErrorCode::ErrUnauthorized, 'Invalid auth token', 401);
            }

            throw $e;
        }
    }
}
