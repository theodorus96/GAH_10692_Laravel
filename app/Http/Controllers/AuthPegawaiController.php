<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\pegawai;
use App\Models\data_user;
use App\Models\user;
use Illuminate\Support\Facades\Hash;

class AuthPegawaiController extends Controller
{

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
        $pegawai = pegawai::where('email', $request->email)->first();

        //cek password
        if (!$pegawai || !Hash::check($request->password, $pegawai->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal!',
                'data' => 'Unauthorized',
            ], 401);
        }

        $role = pegawai::where('email', $request->email)->value('role');

        // Membuat token otentikasi menggunakan Sanctum
        $token = $pegawai->createToken('auth-token', [$role])->plainTextToken;

        $response = [
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'pegawai' => $pegawai,
                'token' => $token,
            ],
        ];

        return response()->json($response, 200);
    }

    //logout
    public function logout(Request $request)
    {
        $request->pegawai()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil!',
        ]);
    }

    //register pegawai
    public function register(Request $request)
    {
        //validasi register
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'email' => 'required|email|unique:pegawai',
            'password' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create pegawai
        $pegawai = pegawai::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));

        if ($pegawai) {
            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil disimpan!',
                'data' => $pegawai
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pegawai gagal disimpan!',
                'data' => $pegawai
            ], 409);
        }
    }


    public function show($id)
    {
        //find pegawai by ID
        $pegawai = pegawai::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data pegawai',
            'data'    => $pegawai
        ], 200);
    }

    public function registerGroup(Request $request)
    {
        //validasi register
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:user',
            'nama' => 'required',
            'no_identitas' => 'required',
            'nomor_telepon' => 'required',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $password = Hash::make($request->password);

        // Membuat entri pengguna dalam tabel "users"
        $user = user::create([
            'email' => $request->email,
        ]);

        // Membuat entri data profil pengguna dalam tabel "data_user"
        $data_user = data_user::create([
            'id_user' => $user->id_user,
            'nama' => $request->nama,
            'no_identitas' => $request->no_identitas,
            'nomor_telepon' => $request->nomor_telepon,
            'alamat' => $request->alamat,
            'role' => "Customer",
            'jenis_customer' => "group",
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

    public function getGroup()
    {
        //get data from table kamar
        $group = data_user::where('jenis_customer', 'group')->get();

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data Customer Group',
            'data'    => $group
        ], 200);
    }
}
