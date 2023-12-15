<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\layanan;

class layananController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get data from table layanan
        $layanan = layanan::select('id_layanan', 'nama', 'deskripsi', 'harga')->get();

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data layanan',
            'data'    => $layanan
        ], 200);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return void
     */
    public function show($id)
    {
        //find layanan by ID
        $layanan = layanan::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data layanan',
            'data'    => $layanan
        ], 200);
    }

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'nama' => 'required|unique:layanan',
            'deskripsi' => 'required',
            'harga' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $layanan = layanan::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
        ]);

        if ($layanan) {
            return response()->json([
                'success' => true,
                'message' => 'layanan Created',
                'data'    => $layanan
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'layanan Failed to Save',
                'data'    => $layanan
            ], 409);
        }
    }
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $layanan
     * @return void
     */
    public function update(Request $request, Int $id)
    {
        $layanan = layanan::find($id);
        if (!$layanan) {
            //data layanan not found
            return response()->json([
                'success' => false,
                'message' => 'layanan Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $layanan->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'harga' => $request->harga,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'layanan Updated',
            'data'    => $layanan
        ], 200);
    }
    /**
     * destroy
     *
     * @param  mixed $layanan
     * @return void
     */
    public function destroy(Int $id)
    {
        $layanan = layanan::find($id);

        if ($layanan) {
            //delete layanan
            $layanan->delete();

            return response()->json([
                'success' => true,
                'message' => 'layanan Deleted',
            ], 200);
        }
        //data layanan not found
        return response()->json([
            'success' => false,
            'message' => 'layanan Not Found',
        ], 404);
    }
}
