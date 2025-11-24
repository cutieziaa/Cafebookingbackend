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

        $validate['password'] = Hash::make($request->password);
        $validate['role'] = 'customer'; // default role

        $user = User::create($validate);

        return response()->json([
            'status' => 'success',
            'message' => 'Register berhasil',
            'user' => $user
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

        $user = User::where('email', $request->email)->first();

        // cek email/password benar?
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau password salah'
            ], 401);
        }

        // buat token Sanctum
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }


    // =============================
    // PROFILE
    // =============================
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => $request->user()
        ]);
    }


    // =============================
    // LOGOUT
    // =============================
    public function logout(Request $request)
    {
        // hapus semua token user
        $request->user()->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }
}
