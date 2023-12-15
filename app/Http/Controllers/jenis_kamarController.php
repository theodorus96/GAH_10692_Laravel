<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\kamar;
use Illuminate\Support\Facades\Validator;
use App\Models\jenis_kamar;

class jenis_kamarController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get data from table jenis_kamar
        $jenis_kamar = jenis_kamar::select('id_jenisKamar', 'jenis', 'rincian_kamar', 'deskripsi', 'ukuran', 'kapasitas', 'harga')->get();

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data jenis_kamar',
            'data'    => $jenis_kamar
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
        //find jenis_kamar by ID
        $jenis_kamar = jenis_kamar::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data jenis_kamar',
            'data'    => $jenis_kamar
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
            'jenis' => 'required:unique:jenis_kamar',
            'rincian_kamar' => 'required',
            'deskripsi' => 'required',
            'ukuran' => 'required',
            'kapasitas' => 'required',
            'harga' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $jenis_kamar = jenis_kamar::create([
            'jenis' => $request->jenis,
            'rincian_kamar' => $request->rincian_kamar,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $request->ukuran,
            'kapasitas' => $request->kapasitas,
            'harga' => $request->harga,
        ]);

        if ($jenis_kamar) {
            return response()->json([
                'success' => true,
                'message' => 'jenis_kamar Created',
                'data'    => $jenis_kamar
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'jenis_kamar Failed to Save',
                'data'    => $jenis_kamar
            ], 409);
        }
    }
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $jenis_kamar
     * @return void
     */
    public function update(Request $request, Int $id)
    {
        $jenis_kamar = jenis_kamar::find($id);
        if (!$jenis_kamar) {
            //data jenis_kamar not found
            return response()->json([
                'success' => false,
                'message' => 'jenis_kamar Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'jenis' => 'required',
            'rincian_kamar' => 'required',
            'deskripsi' => 'required',
            'ukuran' => 'required',
            'kapasitas' => 'required',
            'harga' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $jenis_kamar->update([
            'jenis' => $request->jenis,
            'rincian_kamar' => $request->rincian_kamar,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $request->ukuran,
            'kapasitas' => $request->kapasitas,
            'harga' => $request->harga,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'jenis_kamar Updated',
            'data'    => $jenis_kamar
        ], 200);
    }
    /**
     * destroy
     *
     * @param  mixed $jenis_kamar
     * @return void
     */
    public function destroy(Int $id)
    {
        $jenis_kamar = jenis_kamar::find($id);

        if ($jenis_kamar) {
            //delete jenis_kamar
            $jenis_kamar->delete();

            return response()->json([
                'success' => true,
                'message' => 'jenis_kamar Deleted',
            ], 200);
        }
        //data jenis_kamar not found
        return response()->json([
            'success' => false,
            'message' => 'jenis_kamar Not Found',
        ], 404);
    }
}
