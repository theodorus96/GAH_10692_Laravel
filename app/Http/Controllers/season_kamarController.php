<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\season_kamar;

class season_kamarController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get data from table season_kamar
        $season_kamar = season_kamar::select('id_season', 'id_jenisKamar', 'id_seasonKamar', 'harga_seasonKamar')->get();

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data Season Kamar',
            'data'    => $season_kamar
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
        //find season_kamar by ID
        $season_kamar = season_kamar::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Season Kamar',
            'data'    => $season_kamar
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
            'id_jenisKamar' => 'required:unique:season_kamar',
            'id_season' => 'required:unique:season_kamar',
            'harga_seasonKamar' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $season_kamar = season_kamar::create([
            'id_jenisKamar' => $request->id_jenisKamar,
            'id_season' => $request->id_season,
            'harga_seasonKamar' => $request->harga_seasonKamar,
        ]);

        if ($season_kamar) {
            return response()->json([
                'success' => true,
                'message' => 'Season Kamar Created',
                'data'    => $season_kamar
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Season Kamar Failed to Save',
                'data'    => $season_kamar
            ], 409);
        }
    }
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $season_kamar
     * @return void
     */
    public function update(Request $request, Int $id)
    {
        $season_kamar = season_kamar::find($id);
        if (!$season_kamar) {
            //data season_kamar not found
            return response()->json([
                'success' => false,
                'message' => 'Season Kamar Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_jenisKamar' => 'required',
            'id_season' => 'required',
            'harga_seasonKamar' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $season_kamar->update([
            'id_jenisKamar' => $request->id_jenisKamar,
            'id_season' => $request->id_season,
            'harga_seasonKamar' => $request->harga_seasonKamar,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Season Kamar Updated',
            'data'    => $season_kamar
        ], 200);
    }
    /**
     * destroy
     *
     * @param  mixed $season_kamar
     * @return void
     */
    public function destroy(Int $id)
    {
        $season_kamar = season_kamar::find($id);

        if ($season_kamar) {
            //delete season_kamar
            $season_kamar->delete();

            return response()->json([
                'success' => true,
                'message' => 'Season Kamar Deleted',
            ], 200);
        }
        //data season_kamar not found
        return response()->json([
            'success' => false,
            'message' => 'Season Kamar Not Found',
        ], 404);
    }
}
