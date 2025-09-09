<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use function Illuminate\Log\log;

class AuthApiController extends Controller
{
    /**
     * Check username availability
     */
    public function index(string $username)
    {
        try {
            if (strlen($username) == 0) {
                return response()->json(['error' => 'err_empty_field'], 400);
            }

            $user = User::where('username', $username)->first();
            if ($user != null) {
                return response()->json(['error' => 'err_duplicate_username'], 422);
            }

            return response()->json(['message' => 'username available']);
        } catch (\Throwable $e) {
            log($e->getMessage());
            return response()->json(['error' => 'err_unknown'], 500);
        }
    }

    /**
     * Sign in with password.
     */
    public function store(LoginRequest $request)
    {
        try {
            $request->authenticate();

            return response()->json(['message' => 'success']);
        } catch (ValidationException $e) {
            if ($e->getMessage() == 'These credentials do not match our records.') {
                return response()->json(['message' => 'auth_error'], 401);
            }

            log($e->getMessage());
            return response()->json(['message' => 'unknown_error'], 500);
        }
    }

    /**
     * Sign out current user.
     */
    public function destroy(Request $request)
    {
        try {

            $request->user()->tokens()->delete();
            return response()->json(['message' => 'success']);
        } catch (\Throwable $e) {
            if ($e->getMessage() == 'Call to a member function tokens() on null') {
                return response()->json(['message' => 'auth_error'], 401);
            }

            log($e->getMessage());
            return response()->json(['message' => 'unknown_error'], 500);
        }
    }
}
