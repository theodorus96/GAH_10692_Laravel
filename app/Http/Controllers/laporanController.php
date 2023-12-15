<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservasi;
use Illuminate\Support\Facades\DB;

class laporanController extends Controller
{
    public function getLaporanCustomerBaru(){
        $customerBaru = Reservasi::select(
            DB::raw('count(data_user.id_dataUser) as jumlahCustomer'), 
            DB::raw("DATE_FORMAT(reservasi.tanggal_checkout, '%M') as bulan"),
            DB::raw("MONTH(reservasi.tanggal_checkout) as urutanBulan"),
        )
        ->join('data_user', 'reservasi.id_dataUser', '=', 'data_user.id_dataUser')
        ->groupBy('bulan', 'urutanBulan')
        ->orderBy('urutanBulan', 'asc')
        ->get();
    
        $totalCustomer = $customerBaru->sum('jumlahCustomer');
    
        return response()->json([
            'status' => 'success',
            'message' => 'Get laporan customer baru success',
            'data' => $customerBaru,
            'totalCustomer' => $totalCustomer
        ]);
    }
    

    public function getLaporanPendapatanBulan(){
        $totalPendapatan = Reservasi::select(
            DB::raw("DATE_FORMAT(reservasi.tanggal_checkout, '%M') as bulan"),
            DB::raw("MONTH(reservasi.tanggal_checkout) as urutanBulan"),            
            DB::raw('SUM(CASE WHEN data_user.jenis_customer = "personal" THEN reservasi.total_harga ELSE 0 END) as personal'),
            DB::raw('SUM(CASE WHEN data_user.jenis_customer = "group" THEN reservasi.total_harga ELSE 0 END) as grup'),
            DB::raw('SUM(reservasi.total_harga) as total')
        )
        ->join('data_user', 'reservasi.id_dataUser', '=', 'data_user.id_dataUser')
        ->groupBy('bulan', 'urutanBulan')
        ->orderBy('urutanBulan', 'asc')
        ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get total pendapatan per bulan success',
            'data' => $totalPendapatan
        ]);
    }

    public function getLaporanJumlahTamu(){
        $jumlahTamu = Reservasi::select(
            'jenis_kamar.jenis',
            DB::raw('SUM(CASE WHEN data_user.jenis_customer = "personal" THEN reservasi.jumlah_dewasa + reservasi.jumlah_anak ELSE 0 END) as personal'),
            DB::raw('SUM(CASE WHEN data_user.jenis_customer = "group" THEN reservasi.jumlah_dewasa + reservasi.jumlah_anak ELSE 0 END) as grup'),
            DB::raw('SUM(reservasi.jumlah_dewasa + reservasi.jumlah_anak) as total')
        )
        ->join('transaksi_kamar', 'reservasi.id_reservasi', '=', 'transaksi_kamar.id_reservasi')
        ->join('jenis_kamar', 'transaksi_kamar.id_jenisKamar', '=', 'jenis_kamar.id_jenisKamar')
        ->join('data_user', 'reservasi.id_dataUser', '=', 'data_user.id_dataUser')
        ->groupBy('jenis_kamar.jenis')
        ->orderBy('total', 'desc')
        ->get();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Get total jumlah tamu per jenis kamar success',
            'data' => $jumlahTamu
        ]);
    }
    

    public function getLaporanReservasiTerbanyak(){
        $customerTerbanyak = Reservasi::select(
            'data_user.nama',
            DB::raw('count(reservasi.id_dataUser) as jumlah_reservasi'),
            DB::raw('SUM(reservasi.total_harga) as total_pembayaran'),
        )
        ->join('data_user', 'reservasi.id_dataUser', '=', 'data_user.id_dataUser')
        ->groupBy('data_user.nama')
        ->orderBy('jumlah_reservasi', 'desc')
        ->limit(5)
        ->get();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Get 5 Tamu dengan jumlah reservasi terbanyak sukses',
            'data' => $customerTerbanyak
        ]);
    }
}
