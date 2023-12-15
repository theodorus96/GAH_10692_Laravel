<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transaksi_layanan extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = "transaksi_layanan";
    protected $primaryKey = 'id_transaksiLayanan';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_layanan',
        'id_reservasi',
        'jumlah',
        'total',
        'tanggal',
    ];

    public function layanan()
    {
        return $this->belongsTo(layanan::class, 'id_layanan');
    }

    public function reservasi()
    {
        return $this->belongsTo(reservasi::class, 'id_reservasi');
    }
}
