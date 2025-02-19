<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'birthday' => 'required|date',
            'role' => 'required',
            'address' => 'required',
            'password' => 'required|min:6',
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Data yang akan dimasukkan ke database
        $data = [
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => $request->get('role'),
            'address' => $request->get('address'),
            'birthday' => $request->get('birthday'),
        ];

        try {
            // Menyimpan data ke database
            User::create($data);
            return response()->json(["status" => true, 'message' => 'Data berhasil ditambahkan'], 201);
        } catch (Exception $e) {
            return response()->json(["status" => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getUser()
    {
        try {
            $users = User::all();
            return response()->json([
                'status' => true,
                'message' => 'berhasil load data user',
                'data' => $users,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'gagal load data user. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getDetailUser($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'berhasil load data detail user',
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'gagal load data detail user. ' . $e->getMessage(),
            ], 404);
        }
    }

    public function updateUser($id, Request $request)
    {
        // Validasi input untuk pembaruan
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            "address" => 'required',
            "birthday" => 'required|date',
            'role' => 'required',
            'password' => 'nullable|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        // Ambil data pengguna yang ada
        try {
            $user = User::findOrFail($id);

            // Update hanya kolom yang diberikan dalam request
            $user->name = $request->get('name');
            $user->email = $request->get('email');

            if ($request->filled('password')) {
                $user->password = Hash::make($request->get('password'));
            }

            $user->role = $request->get('role');
            $user->address = $request->get('address');
            $user->birthday = $request->get('birthday');

            $user->save();

            return response()->json([
                "status" => true,
                'message' => 'Data berhasil diupdate'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function hapus_user($id)
    {
        try {
            User::where('id', $id)->delete();
            return response()->json([
                "status" => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "status" => false,
                'message' => 'gagal hapus user. ' . $e,
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard('api')->user();

        return response()->json([
            'status' => true,
            'message' => 'Sukses login',
            'data' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ],
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();
        return response()->json([
            'status' => true,
            'message' => 'Sukses logout',
        ], 200); // Mengembalikan status 200 untuk sukses
    }
}
