<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\kamar;
use App\Models\reservasi;
use Illuminate\Support\Facades\DB;
use App\Models\jenis_kamar;
use App\Models\season;

class kamarController extends Controller
{
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get data from table kamar
        $kamar = kamar::select('id_kamar', 'id_jenisKamar', 'nomor_kamar', 'tipe_kasur')->get();

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data kamar',
            'data'    => $kamar
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
        //find kamar by ID
        $kamar = kamar::find($id);

        //make response JSON
        return response()->json([
            'success' => true,
            'message' => 'Detail Data kamar',
            'data'    => $kamar
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
            'id_jenisKamar' => 'required',
            'nomor_kamar' => 'required|unique:kamar',
            'tipe_kasur' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $kamar = kamar::create([
            'id_jenisKamar' => $request->id_jenisKamar,
            'nomor_kamar' => $request->nomor_kamar,
            'tipe_kasur' => $request->tipe_kasur,
        ]);

        if ($kamar) {
            return response()->json([
                'success' => true,
                'message' => 'kamar Created',
                'data'    => $kamar
            ], 201);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'kamar Failed to Save',
                'data'    => $kamar
            ], 409);
        }
    }
    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $kamar
     * @return void
     */
    public function update(Request $request, Int $id)
    {
        $kamar = kamar::find($id);
        if (!$kamar) {
            //data kamar not found
            return response()->json([
                'success' => false,
                'message' => 'kamar Not Found',
            ], 404);
        }
        //validate form
        $validator = Validator::make($request->all(), [
            'id_jenisKamar' => 'required',
            'nomor_kamar' => 'required|unique:kamar',
            'tipe_kasur' => 'required',
        ]);

        //response error validation
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $kamar->update([
            'id_jenisKamar' => $request->id_jenisKamar,
            'nomor_kamar' => $request->nomor_kamar,
            'tipe_kasur' => $request->tipe_kasur,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'kamar Updated',
            'data'    => $kamar
        ], 200);
    }
    /**
     * destroy
     *
     * @param  mixed $kamar
     * @return void
     */
    public function destroy(Int $id)
    {
        $kamar = kamar::find($id);

        if ($kamar) {
            //delete kamar
            $kamar->delete();

            return response()->json([
                'success' => true,
                'message' => 'kamar Deleted',
            ], 200);
        }
        //data kamar not found
        return response()->json([
            'success' => false,
            'message' => 'kamar Not Found',
        ], 404);
    }


    public function availableRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tanggal_checkin' => 'required|date',
            'tanggal_checkout' => 'required|date|after:tanggal_checkin',
            'jumlah_dewasa' => 'required|integer',
            'jumlah_anak' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $check_in = $request->input('tanggal_checkin');
        $check_out = $request->input('tanggal_checkout');


        $jumlahKamarPerJenisKamar = kamar::select('kamar.id_jenisKamar', DB::raw('count(nomor_kamar) as totalKamar'), 'jenis_kamar.harga', 'jenis_kamar.jenis as jenisKamar')
            ->join('jenis_kamar', 'kamar.id_jenisKamar', '=', 'jenis_kamar.id_jenisKamar')
            ->groupBy('kamar.id_jenisKamar', 'jenis_kamar.harga', 'jenis_kamar.jenis')
            ->with('jenis_kamar')->get();

        $seasonKamar = season::select('season_kamar.harga_seasonKamar', 'season.jenis_season as jenisSeason', 'season_kamar.id_jenisKamar')
            ->join('season_kamar', 'season.id_season', '=', 'season_kamar.id_season')
            ->where(function ($query) use ($check_in, $check_out) {
                $query->where('season.mulai_season', '<=', $check_in)->where('season.akhir_season', '>', $check_in);
            })->get();


        foreach ($jumlahKamarPerJenisKamar as $jumlahKamar) {
            foreach ($seasonKamar as $hargaSeason) {
                if ($hargaSeason->id_jenisKamar == $jumlahKamar->id_jenisKamar) {
                    if ($hargaSeason->jenisSeason == 'High Season') {
                        $Perubahanharga = $jumlahKamar->harga + $hargaSeason->harga_seasonKamar;
                    } else {
                        $Perubahanharga = $jumlahKamar->harga - $hargaSeason->harga_seasonKamar;
                    }
                    $jumlahKamar->harga_seasonKamar = $hargaSeason->harga_seasonKamar;
                    $jumlahKamar->jenisSeason = $hargaSeason->jenisSeason;
                    $jumlahKamar->harga = $Perubahanharga;
                }
            }

            if (is_null($jumlahKamar->jenisSeason)) {
                $jumlahKamar->harga_seasonKamar = null;
                $jumlahKamar->jenisSeason = 'Normal';
                $jumlahKamar->harga = $jumlahKamar->harga;
            }
        }

        $jmlKamarSudahDipakai = reservasi::where(function ($query) use ($check_in, $check_out) {
            $query->where('tanggal_checkin', '<', $check_in)
                ->where('tanggal_checkout', '>', $check_in);
        })->orWhere(function ($query) use ($check_in, $check_out) {
            $query->where('tanggal_checkin', '<', $check_out)
                ->where('tanggal_checkout', '>', $check_out);
        })->orWhere(function ($query) use ($check_in, $check_out) {
            $query->where('tanggal_checkin', '>=', $check_in)
                ->where('tanggal_checkout', '<=', $check_out);
        })->with('transaksi_kamar')->get();

        if ($jmlKamarSudahDipakai !== null) {
            if ($jmlKamarSudahDipakai->count() > 0) {
                foreach ($jmlKamarSudahDipakai as $reservasi) {
                    foreach ($reservasi->transaksi_kamar as $rooms) {
                        $idJK = $rooms->id_jenisKamar;
                        $objJK = $jumlahKamarPerJenisKamar->first(function ($item) use ($idJK) {
                            return $item->id_jenisKamar === $idJK;
                        });

                        if ($objJK && $objJK->totalKamar > 0) {
                            $objJK->totalKamar -= 1;
                        }
                    }
                }

                return response()->json(['status' => 'F', 'message' => 'Sudah ada reservasi lain di tanggal tersebut!', 'data' => $jumlahKamarPerJenisKamar], 200);
            } else {
                return response()->json(['status' => 'T', 'message' => 'Belum ada reservasi', 'data' => $jumlahKamarPerJenisKamar], 200);
            }
        } else {
            // Handle jika $jmlKamarSudahDipakai adalah null
            return response()->json(['status' => 'E', 'message' => 'Terjadi kesalahan dalam pengambilan data'], 500);
        }
    }
}
