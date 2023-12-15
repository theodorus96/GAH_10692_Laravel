<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\season;

class seasonController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get data from table season
        $season = season::select('id_season', 'jenis_season', 'mulai_season', 'akhir_season')->get();

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data season',
            'data'    => $season
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
        //find season by ID
        $season = season::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data season',
            'data'    => $season
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
        $validator = Validator::make($request->all(), [
            'jenis_season' => 'required',
            'mulai_season' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $twoMonthsFromNow = now()->addMonths(2);
                    if ($value < $twoMonthsFromNow) {
                        $fail("$attribute harus lebih dari 2 bulan dari tanggal sekarang.");
                    }
                },
            ],
            'akhir_season' => 'required|date|after:mulai_season',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $season = season::create([
            'jenis_season' => $request->jenis_season,
            'mulai_season' => $request->mulai_season,
            'akhir_season' => $request->akhir_season,
        ]);

        if ($season) {
            return response()->json([
                'success' => true,
                'message' => 'season Created',
                'data'    => $season
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'season Failed to Save',
                'data'    => $season
            ], 409);
        }
    }
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $season
     * @return void
     */
    public function update(Request $request, Int $id)
    {
        $season = season::find($id);
        if (!$season) {
            //data season not found
            return response()->json([
                'success' => false,
                'message' => 'season Not Found',
            ], 404);
        }
        //mulai season harus diset sebelum 2 bulan pelaksanaan season berlangsung

        $validator = Validator::make($request->all(), [
            'jenis_season' => 'required',
            'mulai_season' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $twoMonthsFromNow = now()->addMonths(2);
                    if ($value < $twoMonthsFromNow) {
                        $fail("$attribute harus lebih dari 2 bulan dari tanggal sekarang.");
                    }
                },
            ],
            'akhir_season' => 'required|date|after:mulai_season',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $season->update([
            'jenis_season' => $request->jenis_season,
            'mulai_season' => $request->mulai_season,
            'akhir_season' => $request->akhir_season,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'season Updated',
            'data'    => $season
        ], 200);
    }
    /**
     * destroy
     *
     * @param  mixed $season
     * @return void
     */
    public function destroy(Int $id)
    {
        $season = Season::find($id);

        if ($season) {
            $twoMonthsFromNow = now()->addMonths(2);
            $mulaiSeason = \Carbon\Carbon::parse($season->mulai_season);

            if ($mulaiSeason >= $twoMonthsFromNow) {
                // Hapus season
                $season->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Season Deleted',
                ], 200);
            } else {
                // Tanggal 'mulai_season' kurang dari 2 bulan dari sekarang, tidak dihapus
                return response()->json([
                    'success' => false,
                    'message' => 'Season cannot be deleted, mulai_season is less than 2 months from now.',
                ], 422); // 422 adalah kode status "Unprocessable Entity"
            }
        }

        // Data season not found
        return response()->json([
            'success' => false,
            'message' => 'Season Not Found',
        ], 404);
    }


}
