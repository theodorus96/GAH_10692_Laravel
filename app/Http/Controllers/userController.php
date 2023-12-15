<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\user;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{

    function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //cek email
        $user = user::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan!',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $response = [
            'success' => true,
            'message' => 'Ganti Password berhasil!',
            'data' => [
                'user' => $user,
            ],
        ];

        return response()->json($response, 200);
    }
}
