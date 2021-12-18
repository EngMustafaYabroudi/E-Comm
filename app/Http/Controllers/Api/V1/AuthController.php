<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name'      => 'required|string',
            'email'     => 'required|string|unique:users,email',
            'password'  => 'required|string|confirmed',
        ]);

        $user = User::create([
            'name'     =>  $credentials['name'],
            'email'    =>  $credentials['email'],
            'password' =>  bcrypt($credentials['password'])
        ]);

        $token = $user->createToken('auth')->plainTextToken;

        $response = [
            'user'   =>  $user,
            'token'  =>  $token
        ];
        return response($response, 201);
    }

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
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return [
            'message' => 'has Logout'
        ];
    }
}
