<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'     => 'required|string|email',
            'password'  => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            // he is a real user
            $user = $request->user();

            $token = $user->createToken('auth');

            return ['message' => "Welcome {$user->name}", 'token' => $token->plainTextToken];
        }
    }
}
