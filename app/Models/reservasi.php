<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservasi extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "reservasi";
    protected $primaryKey = 'id_reservasi';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_dataUser',
        'id_pegawai',
        'id_booking',
        'tanggal_checkin',
        'tanggal_checkout',
        'jumlah_dewasa',
        'jumlah_anak',
        'tanggal_reservasi',
        'tanggal_pembayaran',
        'tanggal_cetak',
        'deposit',
        'total_harga',
        'jenis_tamu',
        'status',
        'nomor_rekening',
        'bukti_pembayaran',
        'permintaan',
    ];

    public function data_user()
    {
        return $this->belongsTo(data_user::class, 'id_dataUser');
    }

    public function pegawai()
    {
        return $this->belongsTo(pegawai::class, 'id_pegawai');
    }

    public function invoice()
    {
        return $this->hasOne(invoice::class, 'id_reservasi');
    }

    public function transaksi_kamar()
    {
        return $this->hasMany(transaksi_kamar::class, 'id_reservasi');
    }

    public function transaksi_layanan()
    {
        return $this->hasMany(transaksi_layanan::class, 'id_reservasi');
    }
}
