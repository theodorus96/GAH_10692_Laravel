<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\data_user;
use App\Models\user;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        //validasi register
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:user',
            'password' => 'required|min:8',
            'nama' => 'required',
            'no_identitas' => 'required',
            'nomor_telepon' => 'required',
            'alamat' => 'required',
            'institusi' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $password = Hash::make($request->password);

        // Membuat entri pengguna dalam tabel "users"
        $user = user::create([
            'email' => $request->email,
            'password' => $password,
        ]);

        // Membuat entri data profil pengguna dalam tabel "data_user"
        $data_user = data_user::create([
            'id_user' => $user->id_user,
            'nama' => $request->nama,
            'no_identitas' => $request->no_identitas,
            'nomor_telepon' => $request->nomor_telepon,
            'alamat' => $request->alamat,
            'institusi' => $request->institusi,
            'role' => "Customer",
            'jenis_customer' => "personal",
        ]);

        //response error dan berhasil
        if ($user) {
            return response()->json([
                'success' => true,
                'message' => 'Register berhasil!',
                'data' => $user,
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'user Failed to Save',
                'data'    => $user
            ], 409);
        }
    }

    public function login(Request $request)
    {
        //validasi login
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //cek email
        $user = user::where('email', $request->email)->first();

        //cek password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal!',
                'data' => 'Unauthorized',
            ], 401);
        }

        $dataUser = data_user::where('id_user', $user->id_user)->first();

        // Membuat token otentikasi menggunakan Sanctum
        $token = $user->createToken('auth-token',)->plainTextToken;

        $response = [
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'user' => $user,
                'token' => $token,
                'data_user' => $dataUser,
            ],
        ];

        return response()->json($response, 200);
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil!',
            'data' => null,
        ], 200);
    }

    //get user by id
    public function show(Int $id)
    {
        $user = user::find($id);
        if (!$user) {
            //data user not found
            return response()->json([
                'success' => false,
                'message' => 'User Not Found',
            ], 404);
        }
        $dataUser = data_user::where('id_user', $user->id_user)->first();

        return response()->json([
            'success' => true,
            'message' => 'Detail Data User',
            'data'    => $user,
            'data_user' => $dataUser
        ], 200);
    }

    //update user
    public function updateCustomer(Request $request, Int $id)
    {
        $user = user::find($id);
        $data_user = data_user::where('id_user', $id)->first();
        if (!$user) {
            //data user not found
            return response()->json([
                'success' => false,
                'message' => 'User Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::unique('user')->ignore($user->id_user, 'id_user'),
            ],
            'password' => 'required|min:8',
            'nama' => 'required',
            'no_identitas' => 'required',
            'nomor_telepon' => 'required',
            'alamat' => 'required',
            'institusi' => 'required',

        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $password = Hash::make($request->password);
        $id_user = Auth::id();
        $data_user->update([
            'id_user' => $id_user,
            'nama' => $request->nama,
            'no_identitas' => $request->no_identitas,
            'nomor_telepon' => $request->nomor_telepon,
            'alamat' => $request->alamat,
            'institusi' => $request->institusi,
            'role' => "Customer",
            'jenis_customer' => "personal",
        ]);

        $user->update([
            'email' => $request->email,
            'password' => $password,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User Updated',
            'data'    => $user
        ], 200);
    }

    public function getProfile()
    {
        $user = auth()->user();
        $Customer = $user->data_user;

        return response()->json([
            'success' => true,
            'message' => 'Detail Data User',
            'data'    => $user
        ], 200);
    }
}
