<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // =============================
    // REGISTER
    // =============================
    public function register(Request $request)
    {
        $validate = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name'     => $validate['name'],
            'email'    => $validate['email'],
            'phone'    => $validate['phone'] ?? null,
            'password' => Hash::make($validate['password']),
            'role'     => 'customer', // default user role
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Register berhasil',
            'user'    => $user
        ], 201);
    }



    // =============================
    // LOGIN
    // =============================
    public function login(Request $request)
    {
        $validate = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $validate['email'])->first();

        if (!$user || !Hash::check($validate['password'], $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Generate token
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => $user
        ]);
    }



    // =============================
    // PROFILE (Requires Auth)
    // =============================
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user'   => $request->user()
        ]);
    }



    // =============================
    // LOGOUT (Best Practice)
    // =============================
    public function logout(Request $request)
    {
        // Menghapus token yg sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
