<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    // 1. FUNGSI REGISTER
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Simpan user baru ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Buat token Sanctum untuk user yang baru mendaftar
        $token = $user->createToken('auth_token')->plainTextToken;

        // Kembalikan respon berupa JSON ke React
        return response()->json([
            'message' => 'Register berhasil!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    // 2. FUNGSI LOGIN
    public function login(Request $request)
    {
        // Validasi inputan login
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Cek apakah email dan password cocok dengan database
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Jika cocok, ambil data user tersebut
        $user = User::where('email', $request->email)->firstOrFail();

        // Hapus token lama jika ada (opsional, agar user tidak menimbun banyak token)
        $user->tokens()->delete();

        // Terbitkan token Sanctum baru
        $token = $user->createToken('auth_token')->plainTextToken;

        // Kirim token kembali ke React
        return response()->json([
            'message' => 'Login berhasil!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }

    // 3. FUNGSI LOGOUT
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan saat ini dari database
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Berhasil logout, token telah dihapus.'
        ], 200);
    }
}
