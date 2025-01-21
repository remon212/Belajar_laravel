<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\User; // Memanggil model User
use Hash;
use Exception;
use Illuminate\Validation\Rule; // Pastikan untuk mengimpor Rule

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email', // Validasi email harus valid dan unik
            'birthday' => 'required|date', // Validasi tanggal lahir
            'role' => 'required',
            'address' => 'required',
            'password' => 'required|min:6|confirmed', // Tambahkan validasi untuk konfirmasi password
        ]);

        // Jika validasi gagal
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
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
            return response()->json(["status" => true, 'message' => 'Data berhasil ditambahkan']);
        } catch (Exception $e) {
            return response()->json(["status" => false, 'message' => $e->getMessage()]);
        }
    }

    public function getUser() 
    {
        try {
            $users = User::all(); // Menggunakan all() untuk mengambil semua data user
            return response()->json([
                'status'=>true,
                'message'=>'berhasil load data user',
                'data'=>$users,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'status'=>false,
                'message'=>'gagal load data user. '. $e->getMessage(),
            ]);
        }
    }

    public function getDetailUser($id) 
    {
        try {
            $user = User::findOrFail($id); // Menggunakan findOrFail untuk menemukan user berdasarkan ID
            return response()->json([
                'status'=>true,
                'message'=>'berhasil load data detail user',
                'data'=>$user,
            ]);
        } catch(Exception $e) {
            return response()->json([
                'status'=>false,
                'message'=>'gagal load data detail user. '. $e->getMessage(),
            ]);
        }
    }

    public function updateUser($id, Request $request) 
    {
        // Validasi input untuk pembaruan
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'email'=>['required', Rule::unique('users')->ignore($id)], // Mengabaikan email yang sama saat memperbarui
            "address"=>'required',
            "birthday"=>'required|date', // Validasi tanggal lahir
            'role'=>'required',
            'password'=>'nullable|min:6', // Password tidak wajib diisi saat update
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ]);
        }

        // Ambil data pengguna yang ada
        try {
            $user = User::findOrFail($id); // Temukan pengguna berdasarkan ID
            
            // Update hanya kolom yang diberikan dalam request
            $user->name = $request->get('name');
            $user->email = $request->get('email');
            
            if ($request->filled('password')) { // Hanya hash password jika diisi
                $user->password = Hash::make($request->get('password'));
            }
            
            $user->role = $request->get('role');
            $user->address = $request->get('address');
            $user->birthday = $request->get('birthday');
            
            // Simpan perubahan ke database menggunakan update()
            User::where('id', $id)->update($user->toArray());

            return response()->json([
                "status"=>true,
                'message'=>'Data berhasil diupdate'
            ]);

        } catch (Exception $e) {
            return response()->json([
                "status"=>false,
                'message'=>$e->getMessage()
            ]);
        }
    }

    public function hapus_user($id) 
    {
        try {
            User::where('id', $id)->delete(); // Menghapus pengguna berdasarkan ID
            return response()->json([
                "status"=>true,
                'message'=>'Data berhasil dihapus'
            ]);
        } catch(Exception $e) {
            return response()->json([
                "status"=>false,
                'message'=>'gagal hapus user. '.$e,
            ]);
        }
    }
}
