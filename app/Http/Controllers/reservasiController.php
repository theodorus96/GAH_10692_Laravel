<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Reservasi;
use App\Models\jenis_kamar;
use App\Models\transaksi_kamar;
use App\Models\Transaksi_Layanan;
use App\Models\Layanan;
use App\Models\Invoice;

class reservasiController extends Controller
{
    public function getDetailTransaksi($id)
    {
        $reservasi = Reservasi::with('data_user', 'pegawai')->where('id_reservasi', $id)->first();

        if (is_null($reservasi)) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Get reservation success',
            'data' => $reservasi
        ]);
    }

    public function getRiwayatTransaksi($id)
    {
        $reservasi = Reservasi::where('id_dataUser', $id)->get();

        if ($reservasi->isEmpty()) {
            return response()->json(['message' => 'No reservations found for this customer'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Get riwayat transaksi success',
            'data' => $reservasi
        ]);
    }

    public function getRiwayatTransaksiGroup()
    {
        try {
            $reservasi = Reservasi::with('data_user')
                ->where('jenis_tamu', 'group')
                ->get();
    
            if ($reservasi->isEmpty()) {
                return response()->json(['message' => 'No group reservations found'], 404);
            }
    
            return response()->json([
                'status' => 'success',
                'message' => 'Get riwayat transaksi success',
                'data' => $reservasi
            ]);
        } catch (\Exception $e) {
            // Handle the exception if it occurs
            return response()->json(['message' => 'An error occurred while processing the request'], 500);
        }
    }


    public function addReservasiGroup(Request $request){
        $id_booking = 'G' . date('dmy') . '-' . rand(100, 999);
        $status = "Belum DP";

        $tanggal_reservasi = date('Y-m-d');

        $storeData = $request->all();
        $storeData['id_booking'] = $id_booking;
        $storeData['status'] = $status;
        $storeData['total_harga'] = 0;
        $storeData['id_pegawai'] = 9;
        $storeData['tanggal_reservasi'] = $tanggal_reservasi;
        $storeData['jenis_tamu'] = "group";

        $validate = Validator::make($storeData, [
            'id_dataUser' => 'required',
            'tanggal_checkin' => 'required|before:tanggal_checkout',
            'tanggal_checkout' => 'required|after:tanggal_checkin',
            'jumlah_dewasa' => 'required',
            'jumlah_anak' => 'required',
            'nomor_rekening' => 'required',
            'jenis_kamar' => 'required|array',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $reservasi = Reservasi::create($storeData);
        $totalHargaKamar = 0;


        foreach ($storeData['jenis_kamar'] as $jenisKamar) {
            $hargaKamar = Jenis_Kamar::find($jenisKamar['id_jenisKamar'])->harga;
        
            $transaksi_kamar = new transaksi_kamar();
            $transaksi_kamar->id_reservasi = $reservasi->id_reservasi;
            $transaksi_kamar->id_jenisKamar = $jenisKamar['id_jenisKamar'];
            $transaksi_kamar->jumlah = $jenisKamar['jumlah'];
            
            $subtotalItem = $jenisKamar['jumlah'] * $hargaKamar;
        
            $totalHargaKamar += $subtotalItem;
        
            $transaksi_kamar->subtotal = $subtotalItem;
            $transaksi_kamar->save();
        }
        $totalHargaLayanan = 0;

        if (isset($storeData['layanan'])) {
        foreach ($storeData['layanan'] as $layanan) {
            $hargaLayanan = Layanan::find($layanan['id_layanan'])->harga;
    
            $transaksi_layanan = new Transaksi_Layanan();
            $transaksi_layanan->id_reservasi = $reservasi->id_reservasi;
            $transaksi_layanan->id_layanan = $layanan['id_layanan'];
            $transaksi_layanan->jumlah = $layanan['jumlah'];
    
            $subtotalItemLayanan = $layanan['jumlah'] * $hargaLayanan;
    
            $totalHargaLayanan += $subtotalItemLayanan;
    
            $transaksi_layanan->total = $subtotalItemLayanan;
            $transaksi_layanan->tanggal = date('Y-m-d'); // Ganti dengan tanggal yang sesuai
            $transaksi_layanan->save();
        }
    }
        
    
//    $totalHarga = $totalHargaKamar + $totalHargaLayanan;
    $totalHarga = $totalHargaKamar;

    // Menyimpan total harga ke dalam reservasi
    $reservasi->total_harga = $totalHarga;
    $reservasi->save();

        return response([
            'status' => 'Success',
            'message' => 'Add reservasi success',
            'data' => $reservasi
       ],200);
    }


    public function addReservasi(Request $request){
        $id_booking = 'P' . date('dmy') . '-' . rand(100, 999);
        $status = "Belum DP";

        $tanggal_reservasi = date('Y-m-d');

        $storeData = $request->all();
        $storeData['id_booking'] = $id_booking;
        $storeData['status'] = $status;
        $storeData['total_harga'] = 0;
        $storeData['tanggal_reservasi'] = $tanggal_reservasi;
        $storeData['jenis_tamu'] = "Personal";

        $validate = Validator::make($storeData, [
            'id_dataUser' => 'required',
            'tanggal_checkin' => 'required|before:tanggal_checkout',
            'tanggal_checkout' => 'required|after:tanggal_checkin',
            'jumlah_dewasa' => 'required',
            'jumlah_anak' => 'required',
            'nomor_rekening' => 'required',
            'jenis_kamar' => 'required|array',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $reservasi = Reservasi::create($storeData);
        $totalHargaKamar = 0;


        foreach ($storeData['jenis_kamar'] as $jenisKamar) {
            $hargaKamar = Jenis_Kamar::find($jenisKamar['id_jenisKamar'])->harga;
        
            $transaksi_kamar = new transaksi_kamar();
            $transaksi_kamar->id_reservasi = $reservasi->id_reservasi;
            $transaksi_kamar->id_jenisKamar = $jenisKamar['id_jenisKamar'];
            $transaksi_kamar->jumlah = $jenisKamar['jumlah'];
            
            $subtotalItem = $jenisKamar['jumlah'] * $hargaKamar;
        
            $totalHargaKamar += $subtotalItem;
        
            $transaksi_kamar->subtotal = $subtotalItem;
            $transaksi_kamar->save();
        }
        $totalHargaLayanan = 0;

        if (isset($storeData['layanan'])) {
        foreach ($storeData['layanan'] as $layanan) {
            $hargaLayanan = Layanan::find($layanan['id_layanan'])->harga;
    
            $transaksi_layanan = new Transaksi_Layanan();
            $transaksi_layanan->id_reservasi = $reservasi->id_reservasi;
            $transaksi_layanan->id_layanan = $layanan['id_layanan'];
            $transaksi_layanan->jumlah = $layanan['jumlah'];
    
            $subtotalItemLayanan = $layanan['jumlah'] * $hargaLayanan;
    
            $totalHargaLayanan += $subtotalItemLayanan;
    
            $transaksi_layanan->total = $subtotalItemLayanan;
            $transaksi_layanan->tanggal = date('Y-m-d'); // Ganti dengan tanggal yang sesuai
            $transaksi_layanan->save();
        }
    }
        
    
//    $totalHarga = $totalHargaKamar + $totalHargaLayanan;
    $totalHarga = $totalHargaKamar;

    // Menyimpan total harga ke dalam reservasi
    $reservasi->total_harga = $totalHarga;
    $reservasi->save();

        return response([
            'status' => 'Success',
            'message' => 'Add reservasi success',
            'data' => $reservasi
       ],200);
    }


    public function addLayanan(Request $request){

        $request->validate([
            'id_reservasi' => 'required|exists:reservasi,id_reservasi',
            'id_layanan' => 'required|exists:layanan,id_layanan',
            'jumlah' => 'required|integer|min:1',
        ]);

        $reservasi = Reservasi::find($request->id_reservasi);
        $layananTambahan = Layanan::find($request->id_layanan);

        $totalHargaLayanan = $request->jumlah * $layananTambahan->harga;

        $transaksiLayanan = new Transaksi_Layanan([
            'id_reservasi' => $request->id_reservasi,
            'id_layanan' => $request->id_layanan,
            'jumlah' => $request->jumlah,
            'total' => $totalHargaLayanan,
            'tanggal' => now(),
        ]);

        $reservasi->deposit -= $totalHargaLayanan;
        $reservasi->save();

        $transaksiLayanan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi Layanan Tambahan created successfully'
        ]);

    }

    public function getResumeReservasi($id){
        $reservasi = Reservasi::with('transaksi_kamar', 'transaksi_layanan', 'data_user')
        ->where('id_reservasi', $id)
        ->first();

        if (is_null($reservasi)) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Get reservation success',
            'data' => $reservasi
        ]);
    }

    public function bayarReservasi(Request $request, $id){
        $reservasi = Reservasi::find($id);
        $data_user = $reservasi->data_user;

        if (is_null($reservasi)) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        if($reservasi->status == "Sudah DP"){
            return response()->json(['message' => 'Reservasi sudah dibayar'], 400);
        }
        $reservasi->tanggal_pembayaran = now();

        if($data_user->jenis_customer == "Group" || $data_user->jenis_customer == "group"){
            if($request->uang < 0.5 * $reservasi->total_harga){
                return response()->json(['message' => 'Uang kurang'], 400);
            }else{
                $reservasi->status = "Sudah DP";
                $reservasi->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bayar reservasi success',
                    'data' => $reservasi
                ]);
            }
        }else{
            if($request->uang < $reservasi->total_harga){
                return response()->json(['message' => 'Uang kurang'], 400);
            }else{
                $reservasi->status = "Sudah DP";
                $reservasi->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bayar reservasi success',
                    'data' => $reservasi
                ]);
            }
        }
    }

    public function batalPesan($id){
        $reservasi = Reservasi::find($id);

        if (is_null($reservasi)) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        $reservasi->status = "Batal";
        $reservasi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Batal reservasi success',
            'data' => $reservasi
]);
}

public function Checkin($id)
{
    try {
        $reservasi = Reservasi::find($id);

        if (is_null($reservasi)) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        // Check if the status is "Sudah DP" before allowing check-in
        if ($reservasi->status !== "Sudah DP") {
            return response()->json(['message' => 'Cannot check in. Reservation status is not "Sudah DP"'], 400);
        }

        // Update status to "Checkin"
        $reservasi->status = "Checkin";

        // Deposit additional amount of 300,000
        $reservasi->deposit += 300000;

        $reservasi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Check IN reservasi success',
            'data' => $reservasi
        ]);
    } catch (\Exception $e) {
        // Handle the exception if it occurs
        return response()->json(['message' => 'An error occurred while processing the request'], 500);
    }
}


public function Checkout($id)
{
    try {
        $reservasi = Reservasi::find($id);

        if (is_null($reservasi)) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        // Check if the status is "Checkin" before allowing checkout
        if ($reservasi->status !== "Checkin") {
            return response()->json(['message' => 'Cannot check out. Reservation status is not "Checkin"'], 400);
        }

        // Update status to "Checkout"
        $reservasi->status = "Checkout";
        $reservasi->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Check OUT reservasi success',
            'data' => $reservasi
        ]);
    } catch (\Exception $e) {
        // Handle the exception if it occurs
        return response()->json(['message' => 'An error occurred while processing the request'], 500);
    }
}


public function getPemesanan()
{
    try {
        $reservasi = Reservasi::with('data_user')
        ->whereIn('status', ['Sudah DP', 'Checkin', 'Checkout'])
        ->get();

        if ($reservasi->isEmpty()) {
            return response()->json(['message' => 'No group reservations found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Get riwayat transaksi success',
            'data' => $reservasi
        ]);
    } catch (\Exception $e) {
        // Handle the exception if it occurs
        return response()->json(['message' => 'An error occurred while processing the request'], 500);
    }
}


public function getInvoice($id){
    $reservasi = Reservasi::with('data_user', 'pegawai', 'transaksi_kamar', 'transaksi_layanan')->where('id_reservasi', $id)->first();
    $invoice = Invoice::where('id_reservasi', $id)->first();

    if (is_null($reservasi)) {
        return response()->json(['message' => 'Reservation not found'], 404);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Get nota lunas success',
        'data' => $reservasi,
        'invoice' => $invoice
    ]);
}

}
