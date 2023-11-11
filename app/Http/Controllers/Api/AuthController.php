<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = Auth::user();
                $token = $user->createToken(uniqid())->plainTextToken;
                $user['token'] = $token;
                return response()->json([
                    'message' => 'successfully login',
                    'token' => $token,
                    'data' => $user
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'code' => 403,
                    'message' => 'Opps ! Email or password wrong !'
                ], 403);
            };
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->all();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            if ($user) {
                $token = $user->createToken(uniqid())->plainTextToken;
                $user['token'] = $token;
            }

            return response()->json([
                'success' => true,
                'code' => 201,
                'message' => 'User Successfully Registered !',
                'token' => $token,
                'data' => $user
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }
    }
}
