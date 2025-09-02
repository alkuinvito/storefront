<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use function Illuminate\Log\log;

class LoginApiController extends Controller
{
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
