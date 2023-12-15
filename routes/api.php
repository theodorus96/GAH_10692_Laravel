<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthPegawaiController;
use App\Http\Controllers\kamarController;
use App\Http\Controllers\seasonController;
use App\Http\Controllers\layananController;
use App\Http\Controllers\season_kamarController;
use App\Http\Controllers\reservasiController;
use App\Http\Controllers\userController;
use App\Http\Controllers\jenis_kamarController;
use App\Http\Controllers\laporanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('user/register', [AuthController::class, 'register']);
Route::post('pegawai/register', [AuthPegawaiController::class, 'register']);
Route::post('user/login', [AuthController::class, 'login']);
Route::post('user/logout', [AuthController::class, 'logout']);
Route::post('pegawai/login', [AuthPegawaiController::class, 'login']);
Route::post('pegawai/logout', [AuthPegawaiController::class, 'logout']);
Route::post('user/change-password', [userController::class, 'changePassword']);
Route::get('user/{id}', [AuthController::class, 'show']);


// Route::middleware(['auth:sanctum', 'ability:Admin,SM'])->group(function () {
//     Route::get('user/{id}', [AuthController::class, 'show']);
// });

Route::middleware(['auth:sanctum', 'ability:Customer,SM'])->group(function () {
    Route::post('available', [kamarController::class, 'availableRoom']);
    Route::post('reservasi', [reservasiController::class, 'addReservasi']);
    Route::get('resume/{id}', [reservasiController::class, 'getResumeReservasi']);
    Route::get('reservasi/{id}', [reservasiController::class, 'getDetailTransaksi']);
    Route::post('bayar/{id}', [reservasiController::class, 'bayarReservasi']);
    Route::get('reservasi/riwayat/{id}', [reservasiController::class, 'getRiwayatTransaksi']);
});
//api for kamar
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user', [AuthController::class, 'getProfile']);
    Route::put('user/{id}', [AuthController::class, 'updateCustomer']);
});


//Route::middleware(['auth:sanctum', 'ability:Admin'])->group(function () {
    Route::get('kamar', [kamarController::class, 'index']);
    Route::get('kamar/{id}', [kamarController::class, 'show']);
    Route::post('kamar', [kamarController::class, 'store']);
    Route::put('kamar/{id}', [kamarController::class, 'update']);
    Route::delete('kamar/{id}', [kamarController::class, 'destroy']);
//});

//Route::middleware(['auth:sanctum', 'ability:SM'])->group(function () {

Route::middleware(['auth:sanctum', 'ability:SM'])->group(function () {
    Route::get('season', [seasonController::class, 'index']);
    Route::get('season/{id}', [seasonController::class, 'show']);
    Route::post('season', [seasonController::class, 'store']);
    Route::put('season/{id}', [seasonController::class, 'update']);
    Route::delete('season/{id}', [seasonController::class, 'destroy']);
    Route::get('season/search', [seasonController::class, 'searchSeason']);

    //api for layanan
    Route::get('layanan/{id}', [layananController::class, 'show']);
    Route::post('layanan', [layananController::class, 'store']);
    Route::put('layanan/{id}', [layananController::class, 'update']);
    Route::delete('layanan/{id}', [layananController::class, 'destroy']);

    //api for season_kamar
    Route::get('season_kamar', [season_kamarController::class, 'index']);
    Route::get('season_kamar/{id}', [season_kamarController::class, 'show']);
    Route::post('season_kamar', [season_kamarController::class, 'store']);
    Route::put('season_kamar/{id}', [season_kamarController::class, 'update']);
    Route::delete('season_kamar/{id}', [season_kamarController::class, 'destroy']);

    //registrasi user group
    Route::post('sm/register', [AuthPegawaiController::class, 'registerGroup']);
    Route::get('sm/group', [AuthPegawaiController::class, 'getGroup']);
    Route::post('sm/reservasi', [reservasiController::class, 'addReservasiGroup']);
    Route::get('sm/reservasi/riwayat', [reservasiController::class, 'getRiwayatTransaksiGroup']);

});

//});

// //api for reservasi
// Route::get('reservasi/{id}', [reservasiController::class, 'getDetailTransaksi']);
// Route::get('reservasi/riwayat/{id}', [reservasiController::class, 'getRiwayatTransaksi']);

//api for jenis kamar
Route::get('jenis_kamar', [jenis_kamarController::class, 'index']);
Route::get('layanan', [layananController::class, 'index']);
Route::put('reservasi/{id}', [reservasiController::class, 'batalPesan']);

Route::middleware(['auth:sanctum', 'ability:GM,Owner'])->group(function () {
//api for laporan

});

Route::middleware(['auth:sanctum', 'ability:FO'])->group(function () {
//api for fo
Route::get('fo/pemesanan', [reservasiController::class, 'getPemesanan']);
Route::put('fo/checkin/{id}', [reservasiController::class, 'Checkin']);
Route::put('fo/checkout/{id}', [reservasiController::class, 'Checkout']);
}); 

Route::put('fo/addLayanan', [reservasiController::class, 'addLayanan']);
Route::get('fo/invoice/{id}', [reservasiController::class, 'getInvoice']);

Route::get('laporan/customer-baru', [laporanController::class, 'getLaporanCustomerBaru']);
Route::get('laporan/pendapatan-bulan', [laporanController::class, 'getLaporanPendapatanBulan']);
Route::get('laporan/jumlah-tamu', [laporanController::class, 'getLaporanJumlahTamu']);
Route::get('laporan/customer-terbanyak', [laporanController::class, 'getLaporanReservasiTerbanyak']);

